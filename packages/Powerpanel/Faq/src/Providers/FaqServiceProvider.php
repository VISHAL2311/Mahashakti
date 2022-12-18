<?php

namespace Powerpanel\Faq\Providers;

use Illuminate\Support\ServiceProvider;

class FaqServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->loadViewsFrom(__DIR__.'/../Resources/views', 'faq');
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
            __DIR__.'/../Resources/assets/js/powerpanel' => public_path('resources/pages/scripts/packages/faq'),
        ], 'faq-js');
        
         $this->publishes([
            __DIR__.'/../Resources/assets/js/frontview' => public_path('assets/js/packages/faq'),
        ], 'faq-front-js');
        
        $this->publishes([
            __DIR__ . '/../database/migrations' => database_path('migrations'),
        ], 'faq-migration');

        $this->publishes([
            __DIR__ . '/../database/seeds' => database_path('seeders'),
        ], 'faq-seeds');
        
        $this->handleTranslations();
    }
    
    private function handleTranslations() {

        $this->loadTranslationsFrom(__DIR__.'/../Resources/lang', 'faq');
    }

}
