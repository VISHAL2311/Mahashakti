<?php

namespace Powerpanel\Services\Providers;

use Illuminate\Support\ServiceProvider;

class ServicesServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->loadViewsFrom(__DIR__.'/../Resources/views', 'services');
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        include __DIR__.'/../routes.php';

        $this->publishes([
            __DIR__.'/../Resources/assets/js/powerpanel' => public_path('resources/pages/scripts/packages/services'),
        ], 'services-js');


        $this->publishes([
            __DIR__ . '/../database/migrations' => database_path('migrations'),
        ], 'services-migration');

        $this->publishes([
            __DIR__ . '/../database/seeds' => database_path('seeders'),
        ], 'services-seeds');

        $this->handleTranslations();

    }

    private function handleTranslations() {

        $this->loadTranslationsFrom(__DIR__.'/../Resources/lang', 'services');
    }
}
