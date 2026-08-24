@php
    $roleTicketHistory = \App\Models\ActivityLog::where('ticket_id', $ticket->id)->orderByDesc('created_at')->get();
@endphp
<div class="modal fade" id="ticket-history-modal" tabindex="-1" aria-labelledby="ticketHistoryLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
        <div class="modal-content border-0 border-radius-xl">
            <div class="modal-header border-bottom"><div><small class="text-uppercase text-primary fw-bold">Riwayat Aktivitas</small><h5 class="modal-title fw-bold mt-1" id="ticketHistoryLabel">{{ $ticket->nomor_tiket }}</h5></div><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button></div>
            <div class="modal-body bg-gray-100">
                @forelse($roleTicketHistory as $historyItem)
                    <article class="card border-0 shadow-sm mb-2"><div class="card-body p-3"><div class="d-flex justify-content-between gap-3"><div><strong class="d-block text-dark">{{ ucwords(str_replace('_', ' ', $historyItem->action)) }}</strong><p class="small text-muted mb-0 mt-1" style="white-space:pre-line">{{ $historyItem->detail ?: '-' }}</p></div><time class="small text-muted text-nowrap">{{ \Illuminate\Support\Carbon::parse($historyItem->created_at)->format('d M Y, H:i') }}</time></div></div></article>
                @empty
                    <div class="text-center text-muted py-5"><i class="fas fa-history fa-2x mb-3"></i><div>Belum ada riwayat aktivitas.</div></div>
                @endforelse
            </div>
            <div class="modal-footer border-top"><button type="button" class="btn btn-outline-secondary mb-0" data-bs-dismiss="modal">Tutup</button></div>
        </div>
    </div>
</div>
