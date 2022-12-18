<?php

namespace Powerpanel\Team\Providers;

use Illuminate\Support\ServiceProvider;

class TeamServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->loadViewsFrom(__DIR__.'/../Resources/views', 'team');
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
            __DIR__.'/../Resources/assets/js/powerpanel' => public_path('resources/pages/scripts/packages/team'),
        ], 'team-js');

        $this->publishes([
            __DIR__.'/../Resources/assets/js/frontview' => public_path('assets/js/packages/team'),
        ], 'team-front-js');
        
        $this->publishes([
            __DIR__ . '/../database/migrations' => database_path('migrations'),
        ], 'team-migration');

        $this->publishes([
            __DIR__ . '/../database/seeds' => database_path('seeders'),
        ], 'team-seeds');
        
        $this->handleTranslations();
    }
    
    private function handleTranslations() {

        $this->loadTranslationsFrom(__DIR__.'/../Resources/lang', 'team');
    }

}
