@component('mail::message')
# Status Tiket: {{ $ticket->nomor_tiket }}

Halo {{ $notifiable->name ?? '' }},

Berikut adalah status terbaru untuk tiket Anda.

- **Nomor:** {{ $ticket->nomor_tiket }}
- **Judul:** {{ $ticket->judul ?? '-' }}
- **Status saat ini:** {{ $ticket->status }}


Terima kasih,
{{ config('app.name') }}
@endcomponent
