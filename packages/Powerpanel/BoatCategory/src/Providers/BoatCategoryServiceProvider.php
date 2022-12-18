<?php

namespace Powerpanel\BoatCategory\Providers;

use Illuminate\Support\ServiceProvider;

class BoatCategoryServiceProvider extends ServiceProvider
{
    /**
     * Register boat.
     *
     * @return void
     */
    public function register()
    {
        $this->loadViewsFrom(__DIR__.'/../Resources/views', 'boatcategory');
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
            __DIR__.'/../Resources/assets/js/powerpanel' => public_path('resources/pages/scripts/packages/boatcategory'),
        ], 'boatcategory-js');


        $this->publishes([
            __DIR__ . '/../database/migrations' => database_path('migrations'),
        ], 'boatcategory-migration');

        $this->publishes([
            __DIR__ . '/../database/seeds' => database_path('seeders'),
        ], 'boatcategory-seeds');

        $this->handleTranslations();

    }

    private function handleTranslations() {

        $this->loadTranslationsFrom(__DIR__.'/../Resources/lang', 'boatcategory');
    }
}
