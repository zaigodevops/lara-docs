<?php

namespace ZaigoInfotech\LaraDocs;

use Illuminate\Support\ServiceProvider;

class DocumentManagementServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // Load routes
        $this->loadRoutesFrom(__DIR__ . '/routes/web.php');
        $this->loadMigrationsFrom(__DIR__ . '/Database/Migrations');
        $this->loadViewsFrom(__DIR__ . '/Resources/Views', 'lara-docs');

        // Make views publishable
        $this->publishes([
            __DIR__ . '/resources/views' => resource_path('views/DMS/document-management'),
        ], 'views');

        // Make seeder publishable
        $this->publishes([
            __DIR__ . '/Database/Seeders/DocumentManagementSeeder.php' => database_path('seeders/DocumentManagementSeeder.php'),
        ], 'document-management-seeder');

         // Make css,image publishable
        $this->publishes([
            __DIR__ . '/Public' => public_path('vendor/laradocs'),
        ], 'laradocs-assets');
    }

    public function register()
    {
        //
    }
}