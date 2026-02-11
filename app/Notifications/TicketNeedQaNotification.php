<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\Ticket;

class TicketNeedQaNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $ticket;

    public function __construct(Ticket $ticket)
    {
        $this->ticket = $ticket;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $ticket = $this->ticket;
        return (new MailMessage)
                    ->subject('Tiket perlu dicek oleh QA: ' . $ticket->nomor_tiket)
                    ->markdown('emails.tickets.qa_needed', ['ticket' => $ticket]);
    }
}
