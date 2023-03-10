<?php

namespace Powerpanel\RoleManager\Providers;

use Illuminate\Support\ServiceProvider;

class RoleManagerServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->loadViewsFrom(__DIR__.'/../Resources/views', 'rolemanager');
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
            __DIR__.'/../Resources/assets/js/powerpanel' => public_path('resources/pages/scripts/packages/rolemanager'),
        ], 'rolemanager-js');

        $this->publishes([
            __DIR__ . '/../database/seeds' => database_path('seeders'),
        ], 'rolemanager-seeds');
        
        $this->handleTranslations();
    }
    
    private function handleTranslations() {
        $this->loadTranslationsFrom(__DIR__.'/../Resources/lang', 'rolemanager');
    }

}
