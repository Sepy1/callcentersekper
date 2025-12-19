<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\Ticket;

class TicketQaResolvedNotification extends Notification
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
                    ->subject('Tiket perlu di-close: ' . $ticket->nomor_tiket)
                    ->markdown('emails.tickets.qa_resolved', ['ticket' => $ticket]);
    }
}
