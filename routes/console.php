<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Console\Scheduling\Schedule;
use App\Http\Controllers\ChatController;


Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');



app()->singleton(Schedule::class, function ($app) {
    return tap(new Schedule(), function ($schedule) {
        $schedule->call(function () {
            (new ChatController())->updateQuestionsJson();
        })->everyMinute(); // Runs every minute
    });
});


