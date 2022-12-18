<?php

namespace Powerpanel\GetaEstimateLead\Providers;

use Illuminate\Support\ServiceProvider;

class GetaEstimateLeadServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->loadViewsFrom(__DIR__.'/../Resources/views', 'getaestimatelead');
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
            __DIR__.'/../Resources/assets/js/powerpanel' => public_path('resources/pages/scripts/packages/getaestimatelead'),
        ], 'getaestimatelead-js');

         $this->publishes([
            __DIR__.'/../Resources/assets/js/frontview' => public_path('assets/js/packages/getaestimatelead'),
        ], 'getaestimatelead-front-js');
         
        $this->publishes([
            __DIR__ . '/../database/migrations' => database_path('migrations'),
        ], 'getaestimatelead-migration');

        $this->publishes([
            __DIR__ . '/../database/seeds' => database_path('seeders'),
        ], 'getaestimatelead-seeds');
        
        $this->handleTranslations();
    }
    
    private function handleTranslations() {

        $this->loadTranslationsFrom(__DIR__.'/../Resources/lang', 'getaestimatelead');
    }

}
