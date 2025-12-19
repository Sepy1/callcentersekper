@component('mail::message')
# Anda ditugaskan pada tiket: {{ $ticket->nomor_tiket }}

Halo {{ $notifiable->name ?? '' }},

Anda telah ditugaskan pada tiket berikut:

- **Nomor:** {{ $ticket->nomor_tiket }}
- **Judul:** {{ $ticket->judul ?? '-' }}

@component('mail::button', ['url' => url('officer/tindak-lanjut') . '?ticket_id=' . $ticket->id . '&nomor_tiket=' . urlencode($ticket->nomor_tiket)])
Buka Tindak Lanjut
@endcomponent

Terima kasih,
{{ config('app.name') }}
@endcomponent
