<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Laravel 12 reads the schedule from here (app/Console/Kernel.php is not loaded).
// Run every minute to pick up scheduled posts promptly; the server needs the
// `php artisan schedule:run` cron entry for this to fire.
Schedule::command('posts:publish-scheduled')->everyMinute()->withoutOverlapping();
