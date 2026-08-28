@component('mail::message')
# Tiket Baru: {{ $ticket->nomor_tiket }}

Halo {{ $recipientName ?? $ticket->nama_pelapor ?? 'Pengguna' }},

Tiket berhasil didaftarkan

**Judul:** {{ $ticket->judul ?? '-' }}

**Kategori:** {{ $ticket->kategori_nama }}

@if(isset($recipientType) && $recipientType === 'pelapor')
Terima kasih, tiket Anda telah kami terima. Simpan nomor tiket di atas untuk pelacakan.
@else
Klik tombol di bawah untuk melihat detail tiket pada panel admin / QA.


@endif

Terima kasih,
{{ config('app.name') }}
@endcomponent
