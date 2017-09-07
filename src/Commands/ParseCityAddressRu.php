<?php

namespace Ngtfkx\Laradeck\AddressGenerator\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\DomCrawler\Crawler;

/**
 * Парсер улиц и домов с сайта www.city-address.ru
 *
 * Class ParseCityAddressRu
 * @package Ngtfkx\Laradeck\AddressGenerator\Commands
 */
class ParseCityAddressRu extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'address:city-address-ru {city} {url} {--limit=0}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Parse streets and building numbers from www.city-address.ru';

    /**
     * @var Collection
     */
    protected $streets;

    /**
     * @var int
     */
    protected $cityId;

    /**
     * @var int
     */
    protected $limit;

    /**
     * @var string
     */
    protected $url;

    /**
     * @var string
     */
    protected $name;

    public function __construct()
    {
        $this->streets = new Collection();

        parent::__construct();
    }

    public function handle()
    {
        $this->cityId = $this->argument('city');

        $this->url = $this->argument('url');

        $this->limit = (int)$this->option('limit');

        $this->parse();

        $this->generateFile();

        $this->info('Data was saved');
    }

    /**
     * Парсинг данных
     */
    protected function parse()
    {
        $firstPage = $this->download($this->url);

        $allPages = (int)(new Crawler($firstPage))->filter('.uk-pagination > li')->last()->text();

        $name = (new Crawler($firstPage))->filterXpath('//meta[@name="geo.placename"]')->attr('content');

        $this->name = explode(', ', $name)[0];

        $k = 0;

        for ($i = 1; $i <= $allPages; $i++) {
            $page = $this->download($this->url . '/page-' . $i . '/');

            $this->line('Downloading streets and building numbers from page #' . $i . ' from ' . $allPages);

            $streets = (new Crawler($page))->filter('.c4 > a');

            $streets->each(function ($node) {
                $url = 'http://www.city-address.ru' . $node->attr('href');

                $page = $this->download($url);

                $numbers = collect((new Crawler($page))->filter('.c6 > div > a')->extract('_text'))
                    ->reject(function ($item) {
                        return strpos($item, 'дом ') !== 0;
                    })->map(function ($item) {
                        return str_replace('дом № ', '', $item);
                    })->toArray();

                $this->streets->put($node->text(), $numbers);
            });

            if (++$k == $this->limit) {
                $this->warn('Interrupted by the limit');
                break;
            }
        }
    }

    /**
     * Скачивание страница
     *
     * @param string $url
     * @return string
     */
    protected function download(string $url): string
    {
        $content = file_get_contents($url);

        return $content;
    }

    /**
     * Запись данных в файл
     */
    protected function generateFile()
    {
        $output = '<?php' . PHP_EOL . PHP_EOL;
        $output .= '/**' . PHP_EOL;
        $output .= ' * ' . $this->name . ', Россия' . PHP_EOL;
        $output .= ' */' . PHP_EOL . PHP_EOL;
        $output .= 'return [' . PHP_EOL;
        foreach ($this->streets as $street => $numbers) {
            $glueNumbers = implode('", "', $numbers);
            if (!empty($glueNumbers)) {
                $output .= '    "' . trim($street, ";") . '" => ["' . $glueNumbers . '"],' . PHP_EOL;
            }
        }
        $output .= '];' . PHP_EOL;

        Storage::put($this->cityId . '.php', $output);
    }
}
