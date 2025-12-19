<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\Notification;
use App\Models\Ticket;
use App\Observers\TicketObserver;

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
        /**
         * ======================================
         * REGISTER OBSERVER (HELPDESK)
         * ======================================
         */
        Ticket::observe(TicketObserver::class);

        /**
         * ======================================
         * SHARE NOTIFICATIONS TO ALL VIEWS
         * ======================================
         */
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
                // ❗ sengaja diabaikan agar view tidak error
            }
        });
    }
}
