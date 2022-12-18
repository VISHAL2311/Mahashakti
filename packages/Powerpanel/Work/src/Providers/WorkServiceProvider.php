<?php

namespace Powerpanel\Work\Providers;

use Illuminate\Support\ServiceProvider;

class WorkServiceProvider extends ServiceProvider
{
    /**
     * Register Work.
     *
     * @return void
     */
    public function register()
    {
        $this->loadViewsFrom(__DIR__.'/../Resources/views', 'work');
    }

    /**
     * Bootstrap work.
     *
     * @return void
     */
    public function boot()
    {
        include __DIR__.'/../routes.php';

        $this->publishes([
            __DIR__.'/../Resources/assets/js/powerpanel' => public_path('resources/pages/scripts/packages/work'),
        ], 'work-js');


        $this->publishes([
            __DIR__ . '/../database/migrations' => database_path('migrations'),
        ], 'work-migration');

        $this->publishes([
            __DIR__ . '/../database/seeds' => database_path('seeders'),
        ], 'work-seeds');

        $this->handleTranslations();

    }

    private function handleTranslations() {

        $this->loadTranslationsFrom(__DIR__.'/../Resources/lang', 'work');
    }
}
