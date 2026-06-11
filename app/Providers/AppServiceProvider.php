<?php

namespace App\Providers;

use App\Notifications\Channels\WebPushChannel;
use App\Services\WebPushService;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
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
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        Paginator::useBootstrapFive();

        Notification::extend('webpush', fn () => new WebPushChannel(app(WebPushService::class)));

        View::composer('layouts.app', function ($view): void {
            $view->with(
                'unreadNotificationsCount',
                Auth::check() ? Auth::user()->unreadNotifications()->count() : 0,
            );
        });
    }
}
