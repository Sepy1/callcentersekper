@php
    $officerList = isset($ticket) ? $ticket->officers()->get() : collect();
    $statusLabel = [
        'open' => 'Baru',
        'in_progress' => 'Sedang Diproses',
        'resolved' => 'Selesai oleh QA',
        'closed' => 'Ditutup',
        'rejected' => 'Ditolak',
    ][$ticket->status ?? ''] ?? str_replace('_', ' ', $ticket->status ?? '');
@endphp
<article class="ticket-overview" aria-label="Detail tiket {{ $ticket->nomor_tiket }}">
    <div class="ticket-overview__page-heading">
        <h1>Tindak Lanjut Tiket</h1>
        <p>Kelola dan tindak lanjuti tiket dengan cepat</p>
    </div>
    <header class="ticket-overview__header">
        <div><span class="ticket-overview__eyebrow"><i class="fas fa-ticket-alt"></i> Detail tiket</span><h2>{{ $ticket->nomor_tiket }}</h2></div>
        <div class="ticket-overview__actions">
            <button class="ticket-history-button" type="button" data-bs-toggle="modal" data-bs-target="#ticket-history-modal" id="btn-history" aria-label="Lihat riwayat tiket">
                <i class="fas fa-history"></i><span>Riwayat</span>
            </button>
            <span class="status-pill status-pill--{{ $ticket->status }}">{{ $statusLabel }}</span>
        </div>
    </header>
    <div class="ticket-overview__grid">
        <div class="ticket-field"><span>Nama Pelapor</span><strong>{{ $ticket->nama_pelapor ?: '-' }}</strong></div>
        <div class="ticket-field"><span>Email</span><strong class="text-break">{{ $ticket->email ?: '-' }}</strong></div>
        <div class="ticket-field"><span>No. HP</span><strong>{{ $ticket->hp ?? '-' }} @if(!empty($ticket->hp))<a class="ticket-whatsapp" href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $ticket->hp) }}" target="_blank" rel="noopener" title="Chat pelapor via WhatsApp" aria-label="Chat pelapor via WhatsApp"><i class="fab fa-whatsapp"></i></a>@endif</strong></div>
        <div class="ticket-field"><span>Kategori</span><strong>{{ $ticket->kategori ?: '-' }}</strong></div>
        <div class="ticket-field"><span>Status</span><strong>{{ ucwords($statusLabel) }}</strong></div>
    </div>
    <div class="ticket-overview__divider"></div>
    <div class="ticket-field ticket-field--wide"><span>Officer</span><strong>@forelse($officerList as $officer)<span class="officer-name {{ optional($officer->pivot)->status === 'proses_qa' ? 'is-complete' : '' }}">{{ $officer->name }}</span>@empty - @endforelse</strong></div>
    <div class="ticket-field ticket-field--wide"><span>Judul</span><strong>{{ $ticket->judul ?: '-' }}</strong></div>
    <div class="ticket-field ticket-field--wide"><span>Detail Aduan</span><strong>{{ $ticket->detail ?: '-' }}</strong></div>
    <div class="ticket-field ticket-field--wide"><span>Dibuat</span><strong>{{ $ticket->created_at ? \Illuminate\Support\Carbon::parse($ticket->created_at)->translatedFormat('d F Y, H:i:s') : '-' }}</strong></div>
    <div class="ticket-overview__hint"><i class="fas fa-sync-alt"></i> Klik kartu untuk melihat data pelapor lengkap</div>
</article>
<iframe name="ticket-history-pdf-frame" title="Unduhan PDF riwayat tiket" class="d-none" aria-hidden="true"></iframe>
