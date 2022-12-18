<?php

namespace Powerpanel\FeedbackLead\Providers;

use Illuminate\Support\ServiceProvider;

class FeedbackLeadServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->loadViewsFrom(__DIR__.'/../Resources/views', 'feedbacklead');
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
            __DIR__.'/../Resources/assets/js/powerpanel' => public_path('resources/pages/scripts/packages/feedbacklead'),
        ], 'feedbacklead-js');

        $this->publishes([
            __DIR__ . '/../database/migrations' => database_path('migrations'),
        ], 'feedbacklead-migration');

        $this->publishes([
            __DIR__ . '/../database/seeds' => database_path('seeders'),
        ], 'feedbacklead-seeds');
        
        $this->handleTranslations();
    }
    
    private function handleTranslations() {

        $this->loadTranslationsFrom(__DIR__.'/../Resources/lang', 'feedbacklead');
    }

}
