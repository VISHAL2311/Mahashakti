<?php

namespace Powerpanel\VideoGallery\Providers;

use Illuminate\Support\ServiceProvider;

class VideoGalleryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->loadViewsFrom(__DIR__.'/../Resources/views', 'video-gallery');
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
            __DIR__.'/../Resources/assets/js/powerpanel' => public_path('resources/pages/scripts/packages/videogallery'),
        ], 'videogallery-js');

         
        $this->publishes([
            __DIR__ . '/../database/migrations' => database_path('migrations'),
        ], 'videogallery-migration');

        $this->publishes([
            __DIR__ . '/../database/seeds' => database_path('seeders'),
        ], 'videogallery-seeds');
        
        $this->handleTranslations();
    }
    
     private function handleTranslations() {

        $this->loadTranslationsFrom(__DIR__.'/../Resources/lang', 'videogallery');
    }

}
