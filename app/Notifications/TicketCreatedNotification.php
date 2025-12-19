<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\Ticket;

class TicketCreatedNotification extends Notification
{
    use Queueable;

    protected $ticket;
    protected $recipientType; // 'admin'|'pelapor'|'qa'

    public function __construct(Ticket $ticket, $recipientType = 'admin')
    {
        $this->ticket = $ticket;
        $this->recipientType = $recipientType;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $ticket = $this->ticket;

        // use markdown view for email templates
        return (new MailMessage)
                    ->subject("Tiket baru: {$ticket->nomor_tiket}")
                    ->markdown('emails.tickets.created', ['ticket' => $ticket, 'recipientType' => $this->recipientType]);
    }
}
