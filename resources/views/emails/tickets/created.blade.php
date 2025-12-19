@component('mail::message')
# Tiket Baru: {{ $ticket->nomor_tiket }}

Halo,

Terdapat tiket baru yang masuk.

**Judul:** {{ $ticket->judul ?? '-' }}

@if(isset($recipientType) && $recipientType === 'pelapor')
Terima kasih, tiket Anda telah kami terima. Simpan nomor tiket di atas untuk pelacakan.
@else
Klik tombol di bawah untuk melihat detail tiket pada panel admin / QA.

@component('mail::button', ['url' => url('admin/tickets/'.$ticket->id)])
Lihat Tiket
@endcomponent
@endif

Terima kasih,
{{ config('app.name') }}
@endcomponent
