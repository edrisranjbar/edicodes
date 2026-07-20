<?php

namespace App\Console;

use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Contracts\Console\Kernel as KernelContract;
use Illuminate\Support\Facades\Artisan;
use App\Console\Commands\PublishScheduledPosts;
use App\Console\Commands\SendOnlineNotification;

class Kernel extends ConsoleKernel implements KernelContract
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        PublishScheduledPosts::class,
        SendOnlineNotification::class,
    ];

    /**
     * Define the application's command schedule.
     */
    protected function schedule(\Illuminate\Console\Scheduling\Schedule $schedule): void
    {
        // Run every minute to pick up scheduled posts promptly
        $schedule->command('posts:publish-scheduled')->everyMinute();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
