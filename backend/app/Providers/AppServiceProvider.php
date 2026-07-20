<?php

namespace App\Providers;

use App\Mail\SiteOnlineMail;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (app()->environment('local')) {
            config(['payment.zarinpal.guzzle.verify' => false]);
        }

        if (app()->environment('production') && !Cache::get('site.online.notification.sent', false)) {
            $recipient = env('APP_ONLINE_NOTIFICATION_EMAIL', config('mail.to.address'));
            $recipientName = env('APP_ONLINE_NOTIFICATION_NAME', config('mail.to.name'));

            if (!empty($recipient)) {
                Mail::to($recipient, $recipientName)->send(new SiteOnlineMail(
                    config('app.name'),
                    config('app.url'),
                    now()->setTimezone('Asia/Tehran')->format('Y-m-d H:i:s') . ' (Asia/Tehran)'
                ));
                Cache::forever('site.online.notification.sent', true);
            }
        }
    }
}
