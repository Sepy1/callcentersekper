@component('mail::message')
# Tiket Baru: {{ $ticket->nomor_tiket }}

Halo {{ $ticket->nama_pelapor ?? '' }},

Tiket berhasil didaftarkan

**Judul:** {{ $ticket->judul ?? '-' }}

@if(isset($recipientType) && $recipientType === 'pelapor')
Terima kasih, tiket Anda telah kami terima. Simpan nomor tiket di atas untuk pelacakan.
@else
Klik tombol di bawah untuk melihat detail tiket pada panel admin / QA.


@endif

Terima kasih,
PT BPR BKK Jateng 
@endcomponent
