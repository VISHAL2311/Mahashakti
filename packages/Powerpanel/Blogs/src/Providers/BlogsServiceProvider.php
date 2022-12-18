<?php

namespace Powerpanel\Blogs\Providers;

use Illuminate\Support\ServiceProvider;

class BlogsServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->loadViewsFrom(__DIR__.'/../Resources/views', 'blogs');

        $this->loadTranslationsFrom(__DIR__ . '/../Resources/lang', 'blogs');
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
            __DIR__.'/../Resources/assets/js/powerpanel' => public_path('resources/pages/scripts/packages/blogs'),
        ], 'blogs-js');


        $this->publishes([
            __DIR__ . '/../database/migrations' => database_path('migrations'),
        ], 'blogs-migration');

        $this->publishes([
            __DIR__ . '/../database/seeds' => database_path('seeders'),
        ], 'blogs-seeds');

        $this->handleTranslations();
    }

    private function handleTranslations() {

        $this->loadTranslationsFrom(__DIR__.'/../Resources/lang', 'blogs');
    }

}
