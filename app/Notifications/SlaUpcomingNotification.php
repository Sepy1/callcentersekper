<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class SlaUpcomingNotification extends Notification
{
    use Queueable;

    protected $ticket;

    public function __construct(\App\Models\Ticket $ticket)
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
        $url = url('/admin/tindak-lanjut?ticket_id=' . $ticket->id);

        return (new MailMessage)
            ->subject("[SLA] Tiket H-1: {$ticket->nomor_tiket}")
            ->greeting('Halo Admin,')
            ->line("Terdapat tiket yang akan mencapai SLA dalam 1 hari:")
            ->line("Nomor: {$ticket->nomor_tiket}")
            ->line("Judul: {$ticket->judul}")
            ->line("Dibuat: {$ticket->created_at->format('d F Y')}")
            ->action('Lihat tiket', $url)
            ->line('Silakan tindak lanjuti jika diperlukan.');
    }
}
