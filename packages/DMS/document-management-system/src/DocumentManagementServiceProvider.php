<?php

namespace DMS\DocumentManagementSystem;

use Illuminate\Support\ServiceProvider;

class DocumentManagementServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // Load routes
        $this->loadRoutesFrom(__DIR__ . '/routes/web.php');
        $this->loadMigrationsFrom(__DIR__.'/Database/Migrations');
        $this->loadViewsFrom(__DIR__.'/resources/views', 'document-management');

        // Make views publishable
        $this->publishes([
            __DIR__.'/resources/views' => resource_path('views/DMS/document-management'),
        ], 'views');
    }

    public function register()
    {
        //
    }
}