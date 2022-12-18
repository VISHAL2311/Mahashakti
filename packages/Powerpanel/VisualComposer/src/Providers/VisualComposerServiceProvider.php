<?php

namespace Powerpanel\VisualComposer\Providers;

use Illuminate\Support\ServiceProvider;

class VisualComposerServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->loadViewsFrom(__DIR__.'/../Resources/views', 'visualcomposer');
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
            __DIR__.'/../Resources/assets/js' => public_path('resources/pages/scripts/packages/visualcomposer'),], 'powerpanel-visualcomposer-js');

        $this->publishes([
            __DIR__.'/../Resources/assets/css' => public_path('resources/css/packages/visualcomposer'),
        ], 'powerpanel-visualcomposer-css');

        $this->publishes([
            __DIR__.'/../Resources/assets/images' => public_path('assets/images/packages/visualcomposer'),
        ], 'powerpanel-visualcomposer-img');


        $this->publishes([
            __DIR__ . '/../database/migrations' => database_path('migrations'),
        ], 'visualcomposer-migration');

        $this->publishes([
            __DIR__ . '/../database/seeds' => database_path('seeders'),
        ], 'visualcomposer-seeds');
        
         $this->handleTranslations();
    }
    
     private function handleTranslations() {

        $this->loadTranslationsFrom(__DIR__.'/../Resources/lang', 'visualcomposer');
    }

}
