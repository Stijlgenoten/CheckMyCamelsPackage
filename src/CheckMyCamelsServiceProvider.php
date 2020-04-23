<?php

namespace Stijlgenoten\CheckMyCamels;

use Illuminate\Support\ServiceProvider;
use Stijlgenoten\CheckMyCamels\CheckMyCamels;
use Stijlgenoten\CheckMyCamels\Commands\CheckMyCamelsCommand;


class CheckMyCamelsServiceProvider extends ServiceProvider
{

    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->bootForConsole();
        }
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/checkmycamels.php', 'checkmycamels');

        // Register the service the package provides.
        $this->app->singleton('checkmycamels', function ($app) {
            return new CheckMyCamels;
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['checkmycamels'];
    }
    protected function bootForConsole()
    {
        // Publishing the configuration file.
        $this->publishes([
            __DIR__.'/../config/checkmycamelsserviceprovider.php' => config_path('checkmycamelsserviceprovider.php'),
        ], 'checkmycamelsserviceprovider.config');

        $this->commands([
            CheckMyCamelsCommand::class
        ]);

    }

}
