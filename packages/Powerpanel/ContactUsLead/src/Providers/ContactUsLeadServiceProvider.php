<?php

namespace Powerpanel\ContactUsLead\Providers;

use Illuminate\Support\ServiceProvider;

class ContactUsLeadServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->loadViewsFrom(__DIR__.'/../Resources/views', 'contactuslead');
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
            __DIR__.'/../Resources/assets/js/powerpanel' => public_path('resources/pages/scripts/packages/contactuslead'),
        ], 'contactuslead-js');

         $this->publishes([
            __DIR__.'/../Resources/assets/js/frontview' => public_path('assets/js/packages/contactuslead'),
        ], 'contactuslead-front-js');
         
        $this->publishes([
            __DIR__ . '/../database/migrations' => database_path('migrations'),
        ], 'contactuslead-migration');

        $this->publishes([
            __DIR__ . '/../database/seeds' => database_path('seeders'),
        ], 'contactuslead-seeds');
        
        $this->handleTranslations();
    }
    
    private function handleTranslations() {

        $this->loadTranslationsFrom(__DIR__.'/../Resources/lang', 'contactuslead');
    }

}
