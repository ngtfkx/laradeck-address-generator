<?php

namespace Ngtfkx\Laradeck\AddressGenerator\Commands;

use Illuminate\Console\Command;
use Ngtfkx\Laradeck\AddressGenerator\Generator;

/**
 * Статистика
 *
 * Class ParseCityAddressRu
 * @package Ngtfkx\Laradeck\AddressGenerator\Commands
 */
class GetStat extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'address:stat';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get stat';

    public function handle()
    {
        $generator = new Generator();

        foreach ($generator->getCities() as $key => $city) {
            $generator->clearAddresses()->setCities($city)->getRandomAddress();

            $addresses = $generator->getAllAddresses()[$key];

            $this->info($city . '. Адресов: ' . number_format($addresses->count(), 0, '.', ' '));
        }
    }
}
