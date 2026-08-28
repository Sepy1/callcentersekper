@component('mail::message')
# Tiket Ditutup: {{ $ticket->nomor_tiket }}

Halo {{ $ticket->nama_pelapor ?? '' }},

Tiket Anda telah ditutup.

- **Nomor:** {{ $ticket->nomor_tiket }}
- **Judul:** {{ $ticket->judul ?? '-' }}
- **Kategori:** {{ $ticket->kategori_nama }}
- **Tindak Lanjut:** {{ $ticket->closing_notes ?? '-' }}

Terima kasih telah menggunakan layanan kami.

Salam,
PT BPR BKK Jateng 
@endcomponent
