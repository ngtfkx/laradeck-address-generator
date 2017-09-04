<?php

namespace Ngtfkx\Laradeck\AddressGenerator\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\DomCrawler\Crawler;

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
    protected $description = 'Parse streets and building numbers from http://www.city-address.ru';

    /**
     * @var Collection
     */
    protected $streets;

    public function __construct()
    {
        $this->streets = new Collection();

        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $cityId = $this->argument('city');

        $url = $this->argument('url');

        $limit = (int)$this->option('limit');

        /**
         * Получаем список всех страниц с улицами
         */
        $firstPage = $this->download($url);

        $allPages = (int)(new Crawler($firstPage))->filter('.uk-pagination > li')->last()->text();

        $k = 0;
        for ($i = 1; $i <= $allPages; $i++) {
            /**
             * Получам список улиц на текущей странице
             */
            $page = $this->download($url . '/page-' . $i . '/');

            $this->line('Downloading streets and building numbers from page #' . $i . ' from ' . $allPages);

            $streets = (new Crawler($page))->filter('.c4 > a');

            $streets->each(function ($node) {
                /**
                 * Получаем список домов
                 */
                $url = 'http://www.city-address.ru' . $node->attr('href');

                $page = $this->download($url);

                /**
                 * Обрабатываем список домов
                 */
                $numbers = collect((new Crawler($page))->filter('.c6 > div > a')->extract('_text'))
                    ->reject(function ($item, $key) {
                        return strpos($item, 'дом ') !== 0;
                    })->map(function ($item, $key) {
                        return str_replace('дом № ', '', $item);
                    })->toArray();

                $this->streets->put($node->text(), $numbers);
            });

            if (++$k == $limit) {
                $this->warn('Interrupted by the limit');
                break;
            }
        }

        /**
         * Генерируем контент для файла с данными
         */
        $output = '<?php' . PHP_EOL . PHP_EOL;
        $output .= 'return [' . PHP_EOL;
        foreach ($this->streets as $street => $numbers) {
            $glueNumbers = implode('", "', $numbers);
            if (!empty($glueNumbers)) {
                $output .= '    "' . trim($street, ";") . '" => ["' . $glueNumbers . '"],' . PHP_EOL;
            }
        }
        $output .= '];' . PHP_EOL;

        Storage::put($cityId . '.php', $output);

        $this->info('Data was saved');
    }

    protected function download(string $url)
    {
        $content = file_get_contents($url);

        return $content;
    }
}
