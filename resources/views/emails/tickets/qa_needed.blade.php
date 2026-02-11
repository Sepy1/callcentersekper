@component('mail::message')

# Tiket perlu dicek oleh QA

Tiket **{{ $ticket->nomor_tiket }}** perlu diperiksa dan di-resolve oleh tim QA.

**Judul:** {{ $ticket->judul ?? '-' }}

Silakan buka tiket untuk tindakan lebih lanjut.

@component('mail::button', ['url' => url('qa/tindak-lanjut') . '?ticket_id=' . $ticket->id . '&nomor_tiket=' . urlencode($ticket->nomor_tiket)])
Buka Tiket
@endcomponent

Terima kasih,

{{ config('app.name') }}

@endcomponent
