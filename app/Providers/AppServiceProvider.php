<?php

namespace App\Providers;

use Illuminate\Database\Events\MigrationEnded;
use Illuminate\Database\Events\MigrationsEnded;
use Illuminate\Database\Events\MigrationsStarted;
use Illuminate\Database\Events\MigrationStarted;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app['events']->listen(MigrationsStarted::class, function() {
            Schema::disableForeignKeyConstraints();
        });
        
        $this->app['events']->listen(MigrationsEnded::class, function() {
            Schema::enableForeignKeyConstraints();
        });
        
        $this->app['events']->listen(MigrationStarted::class, function() {
            Schema::disableForeignKeyConstraints();
        });
        
        $this->app['events']->listen(MigrationEnded::class, function() {
            Schema::enableForeignKeyConstraints();
        });
    }
}
