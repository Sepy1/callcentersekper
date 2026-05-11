@extends('layouts.user_type.auth')

@section('content')

<style>

    .timeline-cards {
    display: flex;
    flex-direction: column;
    gap: 16px;
}

/* === CARD === */
.timeline-card {
    background: #ffffff;
    width: 100%;
    max-width: 100%;
    padding: 1rem 1.25rem;
    border-radius: 12px;
    box-shadow: 0 6px 18px rgba(0,0,0,.06);
    position: relative;
    z-index: 2;
}

.timeline-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 24px rgba(0,0,0,.08);
    transition: all .2s ease;
}

.timeline-card:hover {
    transform: translateY(-1px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.08);
}

/* Modern Timeline */
.timeline {
    position: relative;
    padding-left: 2.5rem;
}


/* vertical line */
.timeline::before {
    content: "";
    position: absolute;
    top: 0;
    left: 1rem;
    width: 2px;
    height: 100%;
    background: #e5e7eb;
}

/* === ITEM === */
.timeline-item {
    position: relative;
    display: flex;
    margin-bottom: 1.75rem;
}   

/* === MARKER / DOT === */
.timeline-marker {
    position: absolute;
    left: -1.45rem;
    top: 0.35rem;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    background: #6b7280;
    border: 3px solid #fff;
    z-index: 3;
}

/* === CONTENT === */
.timeline-content {
    width: 100%;
}

.followup-page {
    padding-top: 0.25rem;
    padding-bottom: 1rem;
}

.followup-layout-row {
    row-gap: 1rem;
    align-items: stretch;
}

.followup-main-card,
.followup-chat-card {
    border: 0;
    border-radius: 14px;
    box-shadow: 0 8px 24px rgba(15, 23, 42, 0.06);
    height: 100%;
    margin-bottom: 0 !important;
}

.followup-main-card > .card-body {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.followup-chat-card > .card-body {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
    min-height: 0;
}

.detail-ticket-compact {
    height: 320px;
    min-height: 320px;
}

.detail-ticket-compact .flip-card-front,
.detail-ticket-compact .flip-card-back {
    overflow-y: auto;
}

.detail-ticket-compact .card-body {
    padding: 0.7rem !important;
}

.detail-ticket-compact .card-body h5 {
    font-size: 1rem !important;
    line-height: 1.2;
    margin-top: 0.45rem !important;
    margin-bottom: 0.45rem !important;
}

.detail-ticket-compact .card-body h6 {
    font-size: 0.74rem !important;
    line-height: 1.2;
    margin-bottom: 0.12rem !important;
}

.detail-ticket-compact .card-body p,
.detail-ticket-compact .card-body .small,
.detail-ticket-compact .card-body div {
    font-size: 0.66rem;
    line-height: 1.2;
}

.detail-ticket-compact .card-body .badge {
    font-size: 0.6rem !important;
    padding: 0.28rem 0.45rem !important;
}

.detail-ticket-compact .flip-card-back .mt-1 { margin-top: 0.2rem !important; }
.detail-ticket-compact .flip-card-back .mt-2 { margin-top: 0.35rem !important; }
.detail-ticket-compact .flip-card-back .mt-3 { margin-top: 0.45rem !important; }

.officer-compact-card {
    height: 320px;
    min-height: 320px;
}

.officer-compact-body {
    overflow-y: auto;
}

@media (min-width: 1200px) {
    .followup-chat-card {
        position: sticky;
        top: 1rem;
    }
}
</style>


<div class="container-fluid py-2 followup-page">
	<style>
		/* flip card for ticket detail */
		.flip-card { perspective: 1000px; position: relative; }
		.flip-card .flip-card-inner {
			position: relative;
			width: 100%;
			transition: transform 0.6s;
			transform-style: preserve-3d;
		}
		.flip-card.flipped .flip-card-inner { transform: rotateY(180deg); }
		/* make both sides overlap exactly and hide backface */
		.flip-card-front, .flip-card-back {
			backface-visibility: hidden;
			-webkit-backface-visibility: hidden;
			position: absolute;
			top: 0;
			left: 0;
			width: 100%;
			height: 100%;
			box-sizing: border-box;
		}
		.flip-card-front { z-index: 2; }
		/* hide front text when flipped to avoid mirrored text showing */
		.flip-card.flipped .flip-card-front { visibility: hidden; }
		.flip-card-back {
			transform: rotateY(180deg);
			padding: 1rem;
			background: #f8f9fa;
		}
		/* ensure front card background-color matches back without overriding background-image */
		.flip-card-front .card,
		.flip-card-back {
			background-color: #f8f9fa !important;
		}
		/* visual hint */
		.flip-card { cursor: pointer; min-height: 160px; } /* ensure some min height so absolute children have space */
	</style>

    {{-- Toast Success --}}
    @if(session('success'))
    <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 1080;">
        <div id="toast-notif" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header bg-success text-white rounded-top">
                <strong class="me-auto"><i class="fas fa-check-circle me-2"></i>Sukses</strong>
                <button type="button" class="btn-close btn-close-white ms-2 mb-1" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body bg-white rounded-bottom shadow-sm">
                {{ session('success') }}
            </div>
        </div>
    </div>
    @endif

    <div class="row followup-layout-row">
        <div class="col-12 col-xl-8 d-flex flex-column" style="gap: 1rem;">
            <div class="card followup-main-card h-100 flex-fill">
                <div class="card-header pb-0">
                    <h6 class="mb-0">Tindak Lanjut Tiket</h6>
                </div>
                <div class="card-body d-flex flex-column" style="gap: 1.5rem;">
                    {{-- Filter Nomor Tiket --}}
                    <form id="filter-tiket-form" class="d-flex gap-2 align-items-center mb-4"
                          method="GET"
                          action="{{ route('admin.tindak-lanjut') }}">
                        <div class="input-group input-group-sm border" style="max-width:300px;">
                            <span class="input-group-text bg-white border-0">
                                <i class="fas fa-search text-secondary"></i>
                            </span>
                            <input
                                type="text"
                                id="nomor_tiket"
                                name="nomor_tiket"
                                value="{{ request('nomor_tiket') }}"
                                class="form-control border-0"
                                placeholder="Cari nomor tiket...">
                        </div>
                        <button type="submit" class="btn btn-sm bg-gradient-primary rounded-pill px-3 mb-0">
                            Cari
                        </button>
                        @if(request('nomor_tiket'))
                        <a href="{{ route('admin.tindak-lanjut') }}" class="btn btn-sm bg-gradient-primary rounded-pill px-3 mb-0">
                            Reset
                        </a>
                        @endif
                    </form>

                    {{-- Card Detail Tiket: split into info (left) and officer list (right) --}}
                    @if(!empty($ticket))
                        @php $functions_disabled = in_array($ticket->status, ['closed','rejected']); @endphp
                        <div id="detail-tiket-card" class="mb-2">
                            <div class="row g-3 detail-top-row">
                                <div class="col-md-6">
                                    <div class="flip-card detail-ticket-compact" id="ticket-flip" aria-pressed="false" role="button" tabindex="0" title="Klik untuk melihat informasi pelapor">
                                        <div class="flip-card-inner">
                                            <div class="flip-card-front">
                                                <div class="card h-100">
                                                    <div class="card-body p-0">
                                                        @include('admin.partials.tindak-lanjut-detail', ['ticket' => $ticket])
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="flip-card-back bg-light">
                                                <h6 class="mb-2">Informasi Pelapor</h6>
                                                <div class="small text-muted">Nomor Tiket: {{ $ticket->nomor_tiket }}</div>
                                                <div class="mt-2"><strong>Nama:</strong> {{ $ticket->nama_pelapor }}</div>
                                                <div class="mt-1"><strong>ID KTP:</strong> {{ $ticket->id_ktp ?? '-' }}</div>
                                                <div class="mt-1"><strong>No. Rekening:</strong> {{ $ticket->nomor_rekening ?? '-' }}</div>
                                                <div class="mt-1"><strong>Nama Ibu:</strong> {{ $ticket->nama_ibu ?? '-' }}</div>
                                                <div class="mt-1"><strong>Alamat:</strong> {{ $ticket->alamat ?? '-' }}</div>
                                                <div class="mt-1"><strong>Tempat Lahir:</strong> {{ $ticket->tempat_lahir ?? '-' }}</div>
                                                <div class="mt-1"><strong>Tgl Lahir:</strong> {{ $ticket->tgl_lahir ?? '-' }}</div>
                                                <div class="mt-1"><strong>Kode Kantor:</strong> {{ $ticket->kode_kantor ?? '-' }}</div>
                                                @if(!empty($ticket->upload_ktp))
                                                    <div class="mt-2"><strong>Upload KTP:</strong>
                                                        <a href="{{ asset('storage/'.$ticket->upload_ktp) }}" target="_blank" class="ms-1 small text-decoration-none">{{ \Illuminate\Support\Str::afterLast($ticket->upload_ktp, '/') }}</a>
                                                    </div>
                                                @endif
                                                @if(!empty($ticket->upload_bukti))
                                                    <div class="mt-1"><strong>Upload Bukti:</strong>
                                                        <a href="{{ asset('storage/'.$ticket->upload_bukti) }}" target="_blank" class="ms-1 small text-decoration-none">{{ \Illuminate\Support\Str::afterLast($ticket->upload_bukti, '/') }}</a>
                                                    </div>
                                                @endif
                                                <div class="mt-3 small text-muted">Klik lagi untuk kembali.</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card h-100 officer-compact-card">
                                        <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                                            <h6 class="mb-0">Officer Tertugaskan (TL & Lampiran)</h6>
                                            <div>
                                                <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="modal" data-bs-target="#ticket-history-modal" id="btn-history">
                                                    <i class="fas fa-history me-1"></i>History
                                                </button>
                                            </div>
                                        </div>
                                        <div class="card-body p-2 officer-compact-body">
                                            {{-- QA Summary (highlight orange) --}}
                                            <div class="card mb-2" style="background:#ff7a00;color:#fff;">
                                                <div class="card-body p-2">
                                                    <div class="fw-semibold small mb-1">QA Summary</div>
                                                    <div class="small">{!! nl2br(e($ticket->qa_summary ?? '-')) !!}</div>
                                                </div>
                                            </div>

                                            {{-- Closing Notes (red) --}}
                                            <div class="card mb-2" style="background:#000;color:#fff;">
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
                                            @php
                                                $assignedOfficers = \DB::table('ticket_officer')
                                                    ->where('ticket_id', $ticket->id)
                                                    ->orderBy('created_at')
                                                    ->get();
                                            @endphp
                                            @if($assignedOfficers->isNotEmpty())
                                                <div class="row g-2">
                                                    @foreach($assignedOfficers as $ao)
                                                        @php
                                                            $officer = \App\Models\User::find($ao->officer_id);
                                                            $lampiran = $ao->lampiran ?? null;
                                                        @endphp
                                                        <div class="col-12">
                                                            <div class="card mb-2">
                                                                <div class="card-body p-2 d-flex justify-content-between align-items-start">
                                                                    <div>
                                                                        <div class="fw-semibold">{{ $officer ? $officer->name : $ao->officer_id }}</div>
                                                                        @if(!empty($ao->tl))
                                                                            <div class="small text-muted mt-1">{{ $ao->tl }}</div>
                                                                        @endif
                                                                        @if($lampiran)
                                                                            <div class="mt-1">
                                                                                <a href="{{ asset('storage/'.$lampiran) }}" target="_blank" class="small text-decoration-none">
                                                                                    {{ \Illuminate\Support\Str::afterLast($lampiran, '/') }}
                                                                                </a>
                                                                            </div>
                                                                        @endif
                                                                    </div>
                                                                    <div class="text-end ms-2">
                                                                        <span class="badge bg-{{ $ao->status === 'proses_qa' ? 'success' : ($ao->status === 'cancel_qa' ? 'danger' : 'secondary') }} small">
                                                                            {{ $ao->status }}
                                                                        </span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @else
                                                <div class="text-muted small">Belum ada officer</div>
                                            @endif
                                                {{-- History will be shown in a modal (triggered by the History button) --}}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{-- follow-up row: assign officer form & update status (unchanged) --}}
                        <div class="row" style="gap: 1.5rem 0;">
                            <div class="col-md-6 mb-3">
                                <div class="card flex-fill h-100">
                                    <div class="card-header pb-0">
                                        <h6 class="mb-0">Assign Officer (Multi Assign dengan Mention)</h6>
                                    </div>
                                    <div class="card-body">
                                        <form method="POST" action="{{ route('admin.tindak-lanjut') }}" id="assign-officer-form" class="mb-3" @if($functions_disabled) aria-disabled="true" @endif>
                                            @csrf
                                            <input type="hidden" name="nomor_tiket" value="{{ $ticket->nomor_tiket }}">
                                            <div class="mb-3">
                                                <label class="fw-bold mb-1">Assign ke Officer:</label>
                                                <input type="text"
                                                    id="officer-mention"
                                                    class="form-control"
                                                    placeholder="Ketik nama officer, gunakan @ untuk mention, bisa lebih dari satu"
                                                    autocomplete="off"
                                                    {{ $functions_disabled ? 'disabled' : '' }}
                                                >
                                                <div id="officer-suggestions" class="list-group position-absolute" style="z-index: 10; max-height: 200px; overflow-y: auto; display:none;"></div>
                                                <div id="officer-tags" class="mt-2"></div>
                                                <input type="hidden" name="officer_ids" id="officer-ids">
                                            </div>
                                            <button type="submit" class="btn btn-sm btn-outline-primary" {{ $functions_disabled ? 'disabled' : '' }}>Assign Officer</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="card flex-fill h-100">
                                    <div class="card-header pb-0">
                                        <h6 class="mb-0">Update Status Tiket</h6>
                                    </div>
                                    <div class="card-body">
                                        <form method="POST" action="{{ route('admin.tindak-lanjut') }}" class="d-flex flex-wrap gap-2 align-items-center" id="admin-status-form" @if($functions_disabled) aria-disabled="true" @endif>
                                            @csrf
                                            <input type="hidden" name="nomor_tiket" value="{{ $ticket->nomor_tiket }}">
                                            <label class="mb-0 fw-bold">Status:</label>
                                            <select class="form-select form-select-sm" name="status" id="admin-status-select" style="min-width:140px;" {{ $functions_disabled ? 'disabled' : '' }}>
                                                <option value="open" {{ $ticket->status == 'open' ? 'selected' : '' }}>Open</option>
                                                <option value="in_progress" {{ $ticket->status == 'in_progress' ? 'selected' : '' }}>On Progress</option>
                                                <option value="closed" {{ $ticket->status == 'closed' ? 'selected' : '' }}>Closed</option>
                                                <option value="rejected" {{ $ticket->status == 'rejected' ? 'selected' : '' }}>Rejected</option>
                                            </select>

                                            <!-- hidden fields filled by modal JS when closing -->
                                            <input type="hidden" name="closing_at" id="closing_at">
                                            <input type="hidden" name="tindak_lanjut_closing" id="tindak_lanjut_closing">
                                            <input type="hidden" name="media_closing" id="media_closing">

                                            <button type="submit" class="btn btn-sm btn-outline-success" id="admin-status-submit" {{ $functions_disabled ? 'disabled' : '' }}>Update Status</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                    {{-- End Card Detail Tiket --}}
                </div>
            </div>
        </div>
        <div class="col-12 col-xl-4 d-flex flex-column" style="gap: 1rem;">
            <div class="card followup-chat-card h-100 flex-fill" id="chat-card">
                <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">Chat Tindak Lanjut</h6>
                </div>
                <div class="card-body p-2 d-flex flex-column" style="height:100%; min-height:0;">
                    @if(!empty($ticket))
                        <div id="chat-messages-wrapper" style="flex:1 1 auto; min-height:0;">
                            <div id="chat-messages" class="h-100 overflow-auto" style="padding:8px; background:#f8f9fa; border-radius:8px; display:flex;flex-direction:column;gap:10px;"></div>
                        </div>

                        <div class="chat-controls mt-2 pt-2 border-top" style="flex:0 0 auto;">
                            <div class="d-flex align-items-center" style="gap:.5rem;">
                                <button
                                    id="btn-attach"
                                    type="button"
                                    class="btn btn-outline-secondary d-inline-flex align-items-center justify-content-center"
                                    title="Lampiran"
                                    aria-label="Lampiran"
                                    style="width:36px;height:36px;padding:0;border-radius:8px;"
                                >
                                    <svg style="width:16px;height:16px;display:block;margin:auto;" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M6.354 1.5a3.5 3.5 0 0 1 4.95 4.95l-5.657 5.657a2.5 2.5 0 1 1-3.536-3.536l6.364-6.364" stroke="#6c757d" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M9.95 3.9l-6.364 6.364a1.5 1.5 0 0 0 2.121 2.121l5.657-5.657" stroke="#6c757d" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </button>
                                <input
                                    type="text"
                                    id="chat-input"
                                    class="form-control flex-grow-1"
                                    placeholder="Ketik pesan..."
                                    aria-label="Chat input"
                                    style="height:36px;border-radius:8px;font-size:0.8rem;padding-top:0.35rem;padding-bottom:0.35rem;"
                                />
                                <button
                                    id="btn-send"
                                    type="button"
                                    class="btn btn-primary d-inline-flex align-items-center justify-content-center"
                                    title="Kirim"
                                    aria-label="Kirim"
                                    style="width:36px;height:36px;padding:0;border-radius:8px;"
                                >
                                    <svg style="width:16px;height:16px;display:block;margin:auto;" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M15.854.146a.5.5 0 0 0-.525-.116l-15 6a.5.5 0 0 0 .03.95l6.067 2.023L8.45 14.97a.5.5 0 0 0 .95.03l6-15a.5.5 0 0 0-.116-.525z" fill="#fff"/>
                                    </svg>
                                </button>
                            </div>
                            <input type="file" id="chat-file" class="d-none">
                            <div id="chat-attachment-preview" class="mt-2 small text-muted"></div>
                        </div>
                    @else
                        <div class="text-center text-muted">Cari tiket untuk mulai chat.</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@if(!empty($ticket))
    <!-- Removed stray literal -->
    <!-- Modal: Ticket History -->
    @php
        $history = \App\Models\ActivityLog::where('ticket_id', $ticket->id)
            ->orderBy('created_at')
            ->get();
    @endphp
<!-- Modal: Ticket History -->
@php
    $history = \App\Models\ActivityLog::where('ticket_id', $ticket->id)
        ->orderBy('created_at')
        ->get();

    // If a summary 'ticket_assigned_officers_updated' exists we will hide
    // the per-id assign/unassign entries to avoid duplicates and prefer
    // the human-readable summary (it contains officer names).
    $hasAssignSummary = $history->contains('action', 'ticket_assigned_officers_updated');

    $map = [
        'ticket_created' => ['Tiket dibuat', 'success'],
        'officer_assigned' => ['Officer ditetapkan', 'info'],
        'officer_unassigned' => ['Officer dicabut', 'secondary'],
        'ticket_assigned_officers_updated' => ['Officer diperbarui', 'info'],
        'ticket_updated' => ['Tiket diperbarui', 'warning'],
        'status_changed' => ['Status diperbarui', 'primary'],
        'ticket_deleted' => ['Tiket dihapus', 'danger'],
        'officer_tindak_lanjut' => ['Tindak Lanjut Officer', 'dark'],
        'officer_status_changed' => ['Status Officer', 'primary'],
    ];
@endphp

<div class="modal fade" id="ticket-history-modal" tabindex="-1" aria-labelledby="ticketHistoryLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">
                    Riwayat Tiket — {{ $ticket->nomor_tiket }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
            @if($history->isEmpty())
        <div class="text-muted">Tidak ada riwayat.</div>
    @else
        <div class="timeline">
            @foreach($history as $h)
                @php
                    $when = \Illuminate\Support\Carbon::parse($h->created_at)->format('d M Y H:i');
                    $label = $h->action;
                    $detail = $h->detail ?? '';

                    // Skip verbose per-id assign/unassign entries when a summary exists
                    if (in_array($label, ['officer_assigned','officer_unassigned']) && isset($hasAssignSummary) && $hasAssignSummary) {
                        continue;
                    }

                    // If this is an assign/unassign entry without summary, try to resolve officer id to name
                    if (in_array($label, ['officer_assigned','officer_unassigned']) && $detail) {
                        if (preg_match('/(\d+)/', $detail, $m)) {
                            $u = \App\Models\User::find($m[1]);
                            if ($u) {
                                $detail = ($label === 'officer_assigned' ? 'Assigned officer: ' : 'Unassigned officer: ') . $u->name;
                            }
                        }
                    }

                    // If the log row has a user_id (e.g., officer actions), show the officer name
                    if (empty($detail) && !empty($h->user_id)) {
                        $u = \App\Models\User::find($h->user_id);
                        if ($u) {
                            $detail = $u->name;
                        }
                    } else {
                        // For entries that include user_id plus detail, prepend the name for clarity
                        if (!empty($h->user_id)) {
                            $u = \App\Models\User::find($h->user_id);
                            if ($u) {
                                $detail = $u->name . (trim($detail) ? ' — ' . $detail : '');
                            }
                        }
                    }

                    [$title, $color] = $map[$label] ?? [ucfirst(str_replace('_', ' ', $label)), 'dark'];
                @endphp

                <div class="timeline-item">
                    <div class="timeline-marker bg-{{ $color }}" title="{{ $title }}"></div>
                    <div class="timeline-content">
                        <div class="timeline-card">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="mb-1 fw-semibold text-{{ $color }}">
                                        {{ $title }}
                                    </h6>
                                    @if($detail)
                                        <div class="text-muted small" style="white-space: pre-line;">
                                            {{ $detail }}
                                        </div>
                                    @endif
                                </div>
                                <div class="text-muted small text-end ms-3">
                                    {{ $when }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>

            <div class="modal-footer">
                <button class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>
<script>
(function(){
    const nomor = @json($ticket->nomor_tiket);
    const messagesEl = document.getElementById('chat-messages');
    const input = document.getElementById('chat-input');
    const fileInput = document.getElementById('chat-file');
    const btnAttach = document.getElementById('btn-attach');
    const btnSend = document.getElementById('btn-send');
    const preview = document.getElementById('chat-attachment-preview');
    const token = @json(csrf_token());

    async function fetchMessages(){
        const res = await fetch("{{ url('/chat/messages') }}/" + encodeURIComponent(nomor));
        if(!res.ok) return;
        const msgs = await res.json();
        render(msgs);
    }

    function render(msgs){
        messagesEl.innerHTML = '';
        msgs.forEach(m => {
            const wrap = document.createElement('div');
            wrap.style.display = 'flex';
            wrap.style.flexDirection = 'column';
            wrap.style.maxWidth = '85%';
            wrap.className = (m.user_id === @json(auth()->id())) ? 'align-self-end' : 'align-self-start';

            // show sender name
            const nameEl = document.createElement('div');
            nameEl.style.fontSize = '0.75rem';
            nameEl.style.opacity = '0.8';
            nameEl.style.marginBottom = '4px';
            nameEl.textContent = m.user && m.user.name ? m.user.name : 'User';
            wrap.appendChild(nameEl);

            const bubble = document.createElement('div');
            bubble.className = 'p-2';
            bubble.style.borderRadius = '12px';
            bubble.style.fontSize = '0.85rem';
            bubble.style.whiteSpace = 'pre-wrap';
            if(m.user_id === @json(auth()->id())){
                bubble.style.background = '#0d6efd';
                bubble.style.color = '#fff';
            } else {
                bubble.style.background = '#e9ecef';
                bubble.style.color = '#212529';
            }
            if(m.message) bubble.innerText = m.message;
            if(m.attachment_path){
                const a = document.createElement('a');
                a.href = "{{ asset('storage') }}/" + m.attachment_path;
                a.target = '_blank';
                a.style.display = 'block';
                a.style.marginTop = '6px';
                a.innerText = m.attachment_path.split('/').pop();
                bubble.appendChild(a);
            }
            wrap.appendChild(bubble);
            const ts = document.createElement('div');
            ts.style.fontSize='0.7rem';
            ts.style.opacity='0.6';
            ts.style.marginTop='4px';
            ts.innerText = new Date(m.created_at).toLocaleString();
            wrap.appendChild(ts);
            messagesEl.appendChild(wrap);
        });
        if(messagesEl) messagesEl.scrollTop = messagesEl.scrollHeight;
    }

    btnAttach && btnAttach.addEventListener('click', ()=> fileInput.click());
    fileInput && fileInput.addEventListener('change', e=>{
        const f = e.target.files[0];
        if(!f) return;
        preview.innerHTML = '<div class="small text-truncate">'+f.name+'</div>';
        preview.dataset.file = ''; // flag that we have file (actual upload at send)
    });

    btnSend && btnSend.addEventListener('click', async ()=> {
        const text = (input.value||'').trim();
        const f = fileInput.files[0];
        if(!text && !f) return;
        const fd = new FormData();
        fd.append('nomor_tiket', nomor);
        fd.append('message', text);
        if(f) fd.append('attachment', f);
        const res = await fetch("{{ route('chat.send') }}", {
            method: 'POST',
            credentials: 'same-origin',
            headers: {
                'X-CSRF-TOKEN': token,
                'X-CHAT-SEND': '1',
                'Accept': 'application/json'
            },
            body: fd
        });
        if(res.ok){
            input.value = '';
            fileInput.value = '';
            preview.innerHTML = '';
            await fetchMessages();
        } else {
            alert('Gagal mengirim pesan');
        }
    });

    // initial load + polling every 5s
    fetchMessages();
    setInterval(fetchMessages, 5000);
})();
</script>
@endif
@if(session('success'))
<script>
document.addEventListener('DOMContentLoaded', function() {
    var toastEl = document.getElementById('toast-notif');
    if (toastEl) {
        var toast = new bootstrap.Toast(toastEl, { delay: 4000 });
        toast.show();
    }
});
</script>
@endif
@if(!empty($ticket))
<script>
document.addEventListener('DOMContentLoaded', function(){
    const mentionInput = document.getElementById('officer-mention');
    const suggestionsEl = document.getElementById('officer-suggestions');
    const tagsEl = document.getElementById('officer-tags');
    const hiddenIds = document.getElementById('officer-ids');
    const csrf = '{{ csrf_token() }}';
    let debounceTimer = null;
    const selected = new Map(); // id => name

    function updateHidden() {
        hiddenIds.value = Array.from(selected.keys()).join(',');
    }

    function createTag(id, name) {
        if (selected.has(String(id))) return;
        selected.set(String(id), name);
        const wrap = document.createElement('span');
        wrap.className = 'badge bg-primary me-1 mb-1';
        wrap.style.cursor = 'default';
        wrap.innerHTML = `<span class="me-2">${name}</span><button type="button" class="btn-close btn-close-white btn-sm" aria-label="Remove" style="vertical-align:middle;margin-left:6px;"></button>`;
        const btn = wrap.querySelector('button');
        btn.addEventListener('click', ()=> {
            selected.delete(String(id));
            tagsEl.removeChild(wrap);
            updateHidden();
        });
        tagsEl.appendChild(wrap);
        updateHidden();
    }

    function clearSuggestions(){ suggestionsEl.innerHTML=''; suggestionsEl.style.display='none'; }

    mentionInput.addEventListener('input', function(e){
        const q = this.value.trim();
        clearTimeout(debounceTimer);
        if (!q) { clearSuggestions(); return; }
        debounceTimer = setTimeout(async ()=> {
            try {
                const res = await fetch("{{ route('admin.officers') }}?q=" + encodeURIComponent(q), { credentials: 'same-origin' });
                if (!res.ok) { clearSuggestions(); return; }
                const list = await res.json();
                suggestionsEl.innerHTML = '';
                if (!list.length) { clearSuggestions(); return; }
                list.forEach(u => {
                    const item = document.createElement('button');
                    item.type = 'button';
                    item.className = 'list-group-item list-group-item-action';
                    item.textContent = u.name + (u.email ? ' — ' + u.email : '');
                    item.dataset.id = u.id;
                    item.dataset.name = u.name;
                    item.addEventListener('click', function(){
                        createTag(this.dataset.id, this.dataset.name);
                        mentionInput.value = '';
                        clearSuggestions();
                    });
                    suggestionsEl.appendChild(item);
                });
                suggestionsEl.style.display = 'block';
            } catch(err){
                console.error(err);
                clearSuggestions();
            }
        }, 250);
    });

    // click outside to close suggestions
    document.addEventListener('click', function(ev){
        if (!suggestionsEl.contains(ev.target) && ev.target !== mentionInput) {
            clearSuggestions();
        }
    });

    // optional: prefill tags from existing assignedOfficers list (if any rendered server-side)
    // if you want to prefill, server should render hidden data; skip for now.

});
</script>
@endif
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function(){
    const flip = document.getElementById('ticket-flip');
    if (!flip) return;
    // helper: return true if user selected any text (prevent flip while selecting)
    const isTextSelected = () => {
        try {
            const s = window.getSelection ? window.getSelection().toString() : (document.selection && document.selection.createRange().text) || '';
            return !!(s && s.length);
        } catch(e) { return false; }
    };
    // toggle on click or Enter/Space unless user has text selected
    flip.addEventListener('click', (ev)=> {
        if (isTextSelected()) return;
        flip.classList.toggle('flipped');
    });
    flip.addEventListener('keydown', (e)=> {
        if (e.key === 'Enter' || e.key === ' ') {
            if (isTextSelected()) return;
            e.preventDefault();
            flip.classList.toggle('flipped');
        }
    });

    // ensure flip-card height matches front content to avoid cutting off following cards
    const front = flip.querySelector('.flip-card-front');
    const inner = flip.querySelector('.flip-card-inner');
    const adjustHeight = () => {
        if (!front || !flip) return;
        // measure rendered height of front content
        const maxCompactHeight = 320;
        const h = Math.min(front.scrollHeight, maxCompactHeight);
        flip.style.minHeight = h + 'px';
        if (inner) inner.style.minHeight = h + 'px';
    };
    // run immediately, on load, resize, and when front content changes
    adjustHeight();
    window.addEventListener('load', adjustHeight);
    window.addEventListener('resize', adjustHeight);
    if (window.ResizeObserver) {
        try {
            new ResizeObserver(adjustHeight).observe(front);
        } catch(e){}
    }
});
</script>
@endpush

<!-- Closing modal (Bootstrap) -->
<div class="modal fade" id="closingModal" tabindex="-1" aria-labelledby="closingModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header bg-warning text-white">
        <h5 class="modal-title" id="closingModalLabel">Form Closing Tiket</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label class="form-label fw-bold">Tindak Lanjut (penutup)</label>
          <textarea id="modal-tindak" class="form-control" rows="4" placeholder="Tulis ringkasan closing..."></textarea>
        </div>
        <div class="mb-3">
          <label class="form-label fw-bold">Media Closing</label>
          <select id="modal-media" class="form-select">
            <option value="WhatsApp">WhatsApp</option>
            <option value="Telephone">Telephone</option>
            <option value="Offline">Offline</option>
          </select>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
        <button type="button" class="btn btn-warning btn-sm text-white" id="modal-save-closing">Simpan & Tutup</button>
      </div>
    </div>
  </div>
</div>

@endsection

@if(!empty($ticket))
<script>
document.addEventListener('DOMContentLoaded', function(){
    const statusForm = document.getElementById('admin-status-form');
    const statusSelect = document.getElementById('admin-status-select');
    const modalEl = document.getElementById('closingModal');
    const modal = modalEl ? new bootstrap.Modal(modalEl, { backdrop: 'static', keyboard: false }) : null;
    const modalSave = document.getElementById('modal-save-closing');
    const statusSubmitBtn = document.getElementById('admin-status-submit');

    // flag to bypass modal when submitting programmatically from the modal
    let bypassModal = false;

    if (!statusForm) return;

    statusForm.addEventListener('submit', function(ev){
        if (bypassModal) {
            // allow programmatic submit to go through once
            bypassModal = false;
            return;
        }

        const val = statusSelect ? statusSelect.value : null;
        // if closing, open modal to collect closing info
        if (val === 'closed') {
            ev.preventDefault();
            if (modal) modal.show();
        }
        // otherwise allow normal submit
    });

    // also intercept click on submit button to prevent race conditions
    if (statusSubmitBtn) {
        statusSubmitBtn.addEventListener('click', function(ev){
            const val = statusSelect ? statusSelect.value : null;
            if (val === 'closed') {
                ev.preventDefault();
                if (modal) modal.show();
            }
        });
    }

    // show modal immediately if select changed to closed (optional UX)
    if (statusSelect) {
        statusSelect.addEventListener('change', function(){
            if (this.value === 'closed' && modal) {
                modal.show();
            }
        });
    }

    if (modalSave) {
        modalSave.addEventListener('click', function(){
            // read modal inputs
            const tindak = document.getElementById('modal-tindak').value || '';
            const media = document.getElementById('modal-media').value || '';
            // set hidden inputs
            document.getElementById('tindak_lanjut_closing').value = tindak;
            document.getElementById('media_closing').value = media;
            // closing_at formatted for MySQL DATETIME: YYYY-MM-DD HH:MM:SS
            const dt = new Date();
            const yyyy = dt.getFullYear();
            const mm = String(dt.getMonth()+1).padStart(2,'0');
            const dd = String(dt.getDate()).padStart(2,'0');
            const hh = String(dt.getHours()).padStart(2,'0');
            const mi = String(dt.getMinutes()).padStart(2,'0');
            const ss = String(dt.getSeconds()).padStart(2,'0');
            const formatted = `${yyyy}-${mm}-${dd} ${hh}:${mi}:${ss}`;
            document.getElementById('closing_at').value = formatted;
            // hide modal then submit form (bypass modal handling once)
            if (modal) modal.hide();
            bypassModal = true;
            statusForm.submit();
        });
    }
});
</script>
@endif
<script>
document.addEventListener('DOMContentLoaded', function(){
    const f = document.querySelector('footer');
    if (f) f.remove();
});
</script>

