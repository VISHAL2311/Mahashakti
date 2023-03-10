<?php

namespace Powerpanel\CmsPage\Providers;

use Illuminate\Support\ServiceProvider;

class CmsPageServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->loadViewsFrom(__DIR__.'/../Resources/views', 'cmspage');
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
            __DIR__.'/../Resources/assets/js/powerpanel' => public_path('resources/pages/scripts/packages/cmspage'),
        ], 'cmspage-js');

         
        $this->publishes([
            __DIR__ . '/../database/migrations' => database_path('migrations'),
        ], 'cmspage-migration');

        
        $this->handleTranslations();
    }
    
     private function handleTranslations() {

        $this->loadTranslationsFrom(__DIR__.'/../Resources/lang', 'cmspage');
    }

}
