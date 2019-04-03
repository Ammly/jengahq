<?php

namespace Ammly\Jengahq;

use Illuminate\Support\ServiceProvider;

class JengahqServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadRoutesFrom(__DIR__.'/routes.php');

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
        $this->mergeConfigFrom(__DIR__.'/../config/jengahq.php', 'jengahq');

        // Register the service the package provides.
        $this->app->singleton('jengahq', function ($app) {
            return new Jengahq;
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['jengahq'];
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
            __DIR__.'/../config/jengahq.php' => config_path('jengahq.php'),
        ], 'jengahq.config');

        // Registering package commands.
        // $this->commands([]);
    }
}
