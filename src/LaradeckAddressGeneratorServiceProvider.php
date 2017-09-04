<?php

namespace Ngtfkx\Laradeck\AddressGenerator;

use Illuminate\Support\ServiceProvider;
use Ngtfkx\Laradeck\AddressGenerator\Commands\ParseCityAddressRu;

class LaradeckAddressGeneratorServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                ParseCityAddressRu::class,
            ]);
        }
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}