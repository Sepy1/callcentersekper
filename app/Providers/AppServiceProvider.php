<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Illuminate\Notifications\Events\NotificationSent;
use Illuminate\Notifications\Events\NotificationFailed;
use App\Models\Notification;
use App\Models\Ticket;
use App\Observers\TicketObserver;
use App\Notifications\TicketCreatedNotification;

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

        Event::listen(NotificationSent::class, function (NotificationSent $event): void {
            if ($event->channel === 'mail' && $event->notification instanceof TicketCreatedNotification) {
                Log::info('TICKET EMAIL SENT', [
                    'ticket_id' => $event->notification->ticketId(),
                    'nomor_tiket' => $event->notification->ticketNumber(),
                    'recipient_type' => $event->notification->recipientType(),
                ]);
            }
        });

        Event::listen(NotificationFailed::class, function (NotificationFailed $event): void {
            if ($event->channel === 'mail' && $event->notification instanceof TicketCreatedNotification) {
                Log::error('TICKET EMAIL FAILED', [
                    'ticket_id' => $event->notification->ticketId(),
                    'nomor_tiket' => $event->notification->ticketNumber(),
                    'recipient_type' => $event->notification->recipientType(),
                    'data' => $event->data,
                ]);
            }
        });

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
