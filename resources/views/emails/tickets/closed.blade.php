@component('mail::message')
# Tiket Ditutup: {{ $ticket->nomor_tiket }}

Halo {{ $notifiable->name ?? '' }},

Tiket Anda telah ditutup.

- **Nomor:** {{ $ticket->nomor_tiket }}
- **Judul:** {{ $ticket->judul ?? '-' }}

Terima kasih telah menggunakan layanan kami.

Salam,
{{ config('app.name') }}
@endcomponent
