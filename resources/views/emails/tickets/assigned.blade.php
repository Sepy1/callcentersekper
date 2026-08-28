@component('mail::message')
# Anda ditugaskan pada tiket: {{ $ticket->nomor_tiket }}

Halo {{ $recipientName ?? 'Officer' }},

Anda telah ditugaskan pada tiket berikut:

- **Nomor:** {{ $ticket->nomor_tiket }}
- **Judul:** {{ $ticket->judul ?? '-' }}
- **Kategori:** {{ $ticket->kategori_nama }}

@component('mail::button', ['url' => url('officer/tindak-lanjut') . '?ticket_id=' . $ticket->id . '&nomor_tiket=' . urlencode($ticket->nomor_tiket)])
Buka Tindak Lanjut
@endcomponent

Terima kasih,
{{ config('app.name') }}
@endcomponent
