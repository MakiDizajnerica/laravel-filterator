<?php

namespace MakiDizajnerica\Filterator;

use Illuminate\Support\ServiceProvider;
use MakiDizajnerica\Filterator\FilteratorManager;

class FilteratorServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // $this->mergeConfigFrom(__DIR__ . '/../config/filterator.php', 'filterator');

        $this->app->singleton('makidizajnerica-filterator', fn ($app) => $app->make(FilteratorManager::class));
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // if ($this->app->runningInConsole()) {
        //     $this->publishes([__DIR__ . '/../config/filterator.php' => config_path('filterator.php')], 'filterator-config');
        // }
    }
}
