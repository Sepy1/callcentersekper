@php
    $assignedOfficers = \DB::table('ticket_officer')
        ->where('ticket_id', $ticket->id)
        ->orderBy('created_at')
        ->get();
@endphp

<section class="chat-ticket-summary" aria-labelledby="ticket-summary-title">
    <div class="chat-ticket-summary__header">
        <div>
            <span class="chat-ticket-summary__eyebrow">Summary Tiket</span>
            <h6 id="ticket-summary-title">Officer Tertugaskan, TL & Lampiran</h6>
        </div>
    </div>

    <div class="chat-ticket-summary__body">
        <div class="card mb-2 qa-panel">
            <div class="card-body p-2">
                <div class="fw-semibold small mb-1">QA Summary</div>
                <div class="small">{!! nl2br(e($ticket->qa_summary ?? '-')) !!}</div>
            </div>
        </div>

        <div class="card mb-2 closing-panel">
            <div class="card-body p-2">
                <div class="fw-semibold small mb-1">
                    Closing Notes
                    @if(!empty($ticket->closing_at))
                        <span class="small text-white-50">({{ \Illuminate\Support\Carbon::parse($ticket->closing_at)->format('Y-m-d H:i') }})</span>
                    @endif
                </div>
                <div class="small">{!! nl2br(e($ticket->closing_notes ?? '-')) !!}</div>
            </div>
        </div>

        <div class="chat-ticket-summary__officers">
            @forelse($assignedOfficers as $ao)
                @php
                    $officer = \App\Models\User::find($ao->officer_id);
                    $lampiran = $ao->lampiran ?? null;
                @endphp
                <article class="officer-summary-item">
                    <div class="officer-summary-item__top">
                        <strong><i class="fas fa-user-check me-1"></i>{{ $officer ? $officer->name : $ao->officer_id }}</strong>
                        <span class="badge bg-{{ $ao->status === 'proses_qa' ? 'success' : ($ao->status === 'cancel_qa' ? 'danger' : 'secondary') }}">{{ str_replace('_', ' ', $ao->status) }}</span>
                    </div>
                    @if(!empty($ao->tl))
                        <div class="officer-summary-item__tl"><span>Tindak Lanjut</span><p>{{ $ao->tl }}</p></div>
                    @endif
                    @if($lampiran)
                        <a href="{{ asset('storage/'.$lampiran) }}" target="_blank" rel="noopener" class="officer-summary-item__attachment">
                            <i class="fas fa-paperclip"></i><span>{{ \Illuminate\Support\Str::afterLast($lampiran, '/') }}</span><i class="fas fa-external-link-alt"></i>
                        </a>
                    @endif
                </article>
            @empty
                <div class="officer-summary-empty"><i class="fas fa-user-clock"></i><span>Belum ada officer tertugaskan</span></div>
            @endforelse
        </div>
    </div>
</section>
