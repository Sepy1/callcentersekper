@component('mail::message')
# Status Tiket: {{ $ticket->nomor_tiket }}

Halo {{ $ticket->nama_pelapor ?? '' }},

Berikut adalah status terbaru untuk tiket Anda.

- **Nomor:** {{ $ticket->nomor_tiket }}
- **Judul:** {{ $ticket->judul ?? '-' }}
- **Kategori:** {{ $ticket->kategori_nama }}
- **Status saat ini:** {{ $ticket->status }}


Terima kasih,
PT BPR BKK Jateng 
@endcomponent
