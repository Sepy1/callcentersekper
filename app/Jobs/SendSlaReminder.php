<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendSlaReminder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $ticketId;

    public function __construct(int $ticketId)
    {
        $this->ticketId = $ticketId;
    }

    public function handle()
    {
        $ticket = \App\Models\Ticket::find($this->ticketId);
        if (! $ticket) return;

        // idempotency: don't send if already notified or closed/rejected
        if ($ticket->sla_notified_at) return;
        if (in_array($ticket->status, ['closed','rejected'])) return;

        $admins = \App\Models\User::where('role', 'admin')->get();
        if ($admins->isEmpty()) return;

        try {
            \Illuminate\Support\Facades\Notification::send($admins, new \App\Notifications\SlaUpcomingNotification($ticket));

            $ticket->sla_notified_at = now();
            $ticket->save();
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('SLA REMINDER FAILED', ['ticket_id' => $ticket->id, 'err' => $e->getMessage()]);
        }
    }
}
