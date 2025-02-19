<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Console\Scheduling\Schedule;

class AppServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // Register the scheduled command
        $this->app->booted(function () {
            $schedule = $this->app->make(Schedule::class);
            $schedule->command('questions:update')->everyMinute(); // Run every minute
        });
    }
}
