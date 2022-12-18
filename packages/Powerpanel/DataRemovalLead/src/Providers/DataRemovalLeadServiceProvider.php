<?php

namespace Powerpanel\DataRemovalLead\Providers;

use Illuminate\Support\ServiceProvider;

class DataRemovalLeadServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->loadViewsFrom(__DIR__.'/../Resources/views', 'dataremovallead');
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
            __DIR__.'/../Resources/assets/js/powerpanel' => public_path('resources/pages/scripts/packages/dataremovallead'),
        ], 'dataremovallead-js');

         $this->publishes([
            __DIR__.'/../Resources/assets/js/frontview' => public_path('assets/js/packages/dataremovallead'),
        ], 'dataremovallead-front-js');
         
        $this->publishes([
            __DIR__ . '/../database/migrations' => database_path('migrations'),
        ], 'dataremovallead-migration');

        $this->publishes([
            __DIR__ . '/../database/seeds' => database_path('seeders'),
        ], 'dataremovallead-seeds');
        
        $this->handleTranslations();
    }
    
    private function handleTranslations() {

        $this->loadTranslationsFrom(__DIR__.'/../Resources/lang', 'dataremovallead');
    }

}
