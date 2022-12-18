<?php

namespace Powerpanel\Boat\Providers;

use Illuminate\Support\ServiceProvider;

class BoatServiceProvider extends ServiceProvider
{
    /**
     * Register boat.
     *
     * @return void
     */
    public function register()
    {
        $this->loadViewsFrom(__DIR__.'/../Resources/views', 'boat');
    }

    /**
     * Bootstrap boat.
     *
     * @return void
     */
    public function boot()
    {
        include __DIR__.'/../routes.php';

        $this->publishes([
            __DIR__.'/../Resources/assets/js/powerpanel' => public_path('resources/pages/scripts/packages/boat'),
        ], 'boat-js');


        $this->publishes([
            __DIR__ . '/../database/migrations' => database_path('migrations'),
        ], 'boat-migration');

        $this->publishes([
            __DIR__ . '/../database/seeds' => database_path('seeders'),
        ], 'boat-seeds');

        $this->handleTranslations();

    }

    private function handleTranslations() {

        $this->loadTranslationsFrom(__DIR__.'/../Resources/lang', 'boat');
    }
}
