@component('mail::message')
# Tiket Perlu Di-Close: {{ $ticket->nomor_tiket }}

Halo,

QA telah menandai tiket berikut sebagai resolved dan meminta admin untuk menutup tiket:

- **Nomor:** {{ $ticket->nomor_tiket }}
- **Judul:** {{ $ticket->judul ?? '-' }}

@component('mail::button', ['url' => url('admin/tindak-lanjut') . '?ticket_id=' . $ticket->id . '&nomor_tiket=' . urlencode($ticket->nomor_tiket)])
Buka Tiket untuk Close
@endcomponent

Terima kasih,
{{ config('app.name') }}
@endcomponent
