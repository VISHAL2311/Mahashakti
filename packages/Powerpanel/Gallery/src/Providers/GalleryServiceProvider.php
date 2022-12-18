<?php

namespace Powerpanel\Gallery\Providers;

use Illuminate\Support\ServiceProvider;

class GalleryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->loadViewsFrom(__DIR__.'/../Resources/views', 'gallery');
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
            __DIR__.'/../Resources/assets/js/powerpanel' => public_path('resources/pages/scripts/packages/gallery'),
        ], 'gallery-js');
        
        $this->publishes([
            __DIR__.'/../Resources/assets/css/powerpanel' => public_path('resources/layouts/layout4/css'),
        ], 'multi-gallery-css');
         
        $this->publishes([
            __DIR__ . '/../database/migrations' => database_path('migrations'),
        ], 'gallery-migration');

        $this->publishes([
            __DIR__ . '/../database/seeds' => database_path('seeders'),
        ], 'gallery-seeds');
        
        $this->handleTranslations();
    }
    
     private function handleTranslations() {

        $this->loadTranslationsFrom(__DIR__.'/../Resources/lang', 'gallery');
    }

}
