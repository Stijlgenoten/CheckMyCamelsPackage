<?php

namespace Stijlgenoten\CheckMyCamels;

use Illuminate\Support\ServiceProvider;
use Stijlgenoten\CheckMyCamels\Commands\CheckMyCamels;

class CheckMyCamelsServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        // $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'stijlgenoten');
        // $this->loadViewsFrom(__DIR__.'/../resources/views', 'stijlgenoten');
        // $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        // $this->loadRoutesFrom(__DIR__.'/routes.php');

        // Publishing is only necessary when using the CLI.
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
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [];
    }
    
    /**
     * Console-specific booting.
     *
     * @return void
     */
    protected function bootForConsole()
    {
        // Publishing the configuration file.
        $this->publishes([
            __DIR__.'/../config/checkmycamels.php' => config_path('checkmycamels.php'),
        ], 'checkmycamels.config');

        // Publish: Artisan check-my-camels command
        $this->commands([
            CheckMyCamels::class
        ]);
    }
}
