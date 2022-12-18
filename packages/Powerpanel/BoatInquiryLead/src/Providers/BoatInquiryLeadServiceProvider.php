<?php

namespace Powerpanel\BoatInquiryLead\Providers;

use Illuminate\Support\ServiceProvider;

class BoatInquiryLeadServiceProvider extends ServiceProvider
{
    /**
     * Register boats.
     *
     * @return void
     */
    public function register()
    {
        $this->loadViewsFrom(__DIR__.'/../Resources/views', 'boatinquirylead');
    }

    /**
     * Bootstrap boats.
     *
     * @return void
     */
    public function boot()
    {
        include __DIR__.'/../routes.php';

        $this->publishes([
            __DIR__.'/../Resources/assets/js/powerpanel' => public_path('resources/pages/scripts/packages/boatinquirylead'),
        ], 'boatinquirylead-js');

         $this->publishes([
            __DIR__.'/../Resources/assets/js/frontview' => public_path('assets/js/packages/boatinquirylead'),
        ], 'boatinquirylead-front-js');
         
        $this->publishes([
            __DIR__ . '/../database/migrations' => database_path('migrations'),
        ], 'boatinquirylead-migration');

        $this->publishes([
            __DIR__ . '/../database/seeds' => database_path('seeders'),
        ], 'boatinquirylead-seeds');
        
        $this->handleTranslations();
    }
    
    private function handleTranslations() {

        $this->loadTranslationsFrom(__DIR__.'/../Resources/lang', 'boatinquirylead');
    }

}
