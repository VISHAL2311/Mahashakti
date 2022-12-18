<?php

namespace Powerpanel\ServicesCategory\Providers;

use Illuminate\Support\ServiceProvider;

class ServicesCategoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->loadViewsFrom(__DIR__.'/../Resources/views', 'servicescategory');
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
            __DIR__.'/../Resources/assets/js/powerpanel' => public_path('resources/pages/scripts/packages/servicescategory'),
        ], 'servicescategory-js');


        $this->publishes([
            __DIR__ . '/../database/migrations' => database_path('migrations'),
        ], 'servicescategory-migration');

        $this->publishes([
            __DIR__ . '/../database/seeds' => database_path('seeders'),
        ], 'servicescategory-seeds');

        $this->handleTranslations();

    }

    private function handleTranslations() {

        $this->loadTranslationsFrom(__DIR__.'/../Resources/lang', 'servicescategory');
    }
}
