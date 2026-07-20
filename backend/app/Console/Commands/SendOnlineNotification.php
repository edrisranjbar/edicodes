<?php

namespace App\Console\Commands;

use App\Mail\SiteOnlineMail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendOnlineNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-online-notification';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send an email notification that the application is online';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $recipient = env('APP_ONLINE_NOTIFICATION_EMAIL', config('mail.to.address'));
        $recipientName = env('APP_ONLINE_NOTIFICATION_NAME', config('mail.to.name'));
        $appName = config('app.name');
        $appUrl = config('app.url');
        $timestamp = now()->setTimezone('Asia/Tehran')->format('Y-m-d H:i:s') . ' (Asia/Tehran)';

        if (empty($recipient)) {
            $this->error('No recipient configured for online notification.');
            return 1;
        }

        try {
            Mail::to($recipient, $recipientName)->send(new SiteOnlineMail($appName, $appUrl, $timestamp));
            $this->info("Online notification email sent to {$recipient}");
            return 0;
        } catch (\Throwable $e) {
            $this->error('Failed to send online notification: ' . $e->getMessage());
            return 1;
        }
    }
}
