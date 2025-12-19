<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\Notification;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // share notifications to all views (safe: auth() ready when views rendered)
        View::composer('*', function ($view) {
            try {
                if (auth()->check()) {
                    $userId = auth()->id();
                    $latest = Notification::where('user_id', $userId)
                        ->orderBy('created_at', 'desc')
                        ->take(5)
                        ->get();
                    $unreadCount = Notification::where('user_id', $userId)
                        ->where('is_read', false)
                        ->count();
                    $view->with('sidebar_notifications', $latest)
                         ->with('sidebar_notifications_unread_count', $unreadCount);
                }
            } catch (\Throwable $e) {
                // ignore to avoid breaking pages
            }
        });
    }
}
