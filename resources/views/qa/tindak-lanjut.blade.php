@extends('layouts.user_type.auth')

@section('content')
<div class="container-fluid py-2 followup-page">
	<style>
		/* flip card for ticket detail (same as admin) */
		.flip-card { perspective: 1000px; position: relative; }
		.flip-card .flip-card-inner { position: relative; width:100%; transition: transform 0.6s; transform-style: preserve-3d; }
		.flip-card.flipped .flip-card-inner { transform: rotateY(180deg); }
		.flip-card-front, .flip-card-back { backface-visibility: hidden; -webkit-backface-visibility: hidden; position: absolute; top:0; left:0; width:100%; height:100%; box-sizing:border-box; }
		.flip-card-front { z-index:2; }
		.flip-card.flipped .flip-card-front { visibility: hidden; }
		.flip-card-back { transform: rotateY(180deg); padding:1rem; background:#f8f9fa; }
		/* ensure front card background-color matches back without overriding background-image */
		.flip-card-front .card,
		.flip-card-back {
			background-color: #f8f9fa !important;
		}
		.flip-card-front { padding:0; }
		.flip-card { cursor:pointer; min-height:160px; }

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
	@include('partials.followup-role-interface')
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
                    <form id="filter-tiket-form" class="d-flex gap-2 align-items-center mb-4"
                          method="GET"
                          action="{{ url('qa/tindak-lanjut') }}">
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
                        <a href="{{ url('qa/tindak-lanjut') }}" class="btn btn-sm bg-gradient-primary rounded-pill px-3 mb-0">
                            Reset
                        </a>
                        @endif
                    </form>
                    @if(!empty($ticket))
                        @php $functions_disabled = in_array($ticket->status, ['closed','rejected']); @endphp
                        {{-- Split detail tiket: left = tiket detail, right = officer tl & lampiran --}}
                        <div id="detail-tiket-card" class="mb-2">
                            <div class="row g-3 detail-top-row">
                                <div class="col-md-6">
                                    <div class="flip-card detail-ticket-compact" id="ticket-flip-qa" aria-pressed="false" role="button" tabindex="0" aria-label="Klik untuk melihat informasi pelapor">
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
                                                <div class="mt-2"><strong>Nama:</strong> {{ $ticket->nama_pelapor ?? '-' }}</div>
                                                <div class="mt-1"><strong>ID KTP:</strong> {{ $ticket->id_ktp ?? '-' }}</div>
                                                @if(!empty($ticket->id_ktp))
                                                    @php
                                                        $cifs = \App\Models\Nasabah::where('no_ktp', $ticket->id_ktp)->pluck('cif')->toArray();
                                                        $cifCount = count($cifs);
                                                        $visibleCifs = array_slice($cifs, 0, 3);
                                                        $hiddenCifs = array_slice($cifs, 3);
                                                    @endphp
                                                    <div class="mt-1"><strong>CIF:</strong>
                                                        @if($cifCount === 0)
                                                            -
                                                        @else
                                                            @foreach($visibleCifs as $c)
                                                                <span class="badge bg-info text-dark me-1">{{ $c }}</span>
                                                            @endforeach
                                                            @if(count($hiddenCifs) > 0)
                                                                <span id="qa-more-cifs" class="d-none">
                                                                    @foreach($hiddenCifs as $c)
                                                                        <span class="badge bg-info text-dark me-1">{{ $c }}</span>
                                                                    @endforeach
                                                                </span>
                                                                <button type="button" id="qa-btn-show-more-cifs" class="btn btn-sm btn-link">+{{ count($hiddenCifs) }} more</button>
                                                            @endif
                                                        @endif
                                                    </div>
                                                @endif
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
                                        <div class="card-header pb-0">
                                            <h6 class="mb-0">Officer Tertugaskan (TL & Lampiran)</h6>
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
                                            @if(isset($assignedOfficers) && $assignedOfficers->isNotEmpty())
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
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Compact: Summary QA | Update Status (two columns) --}}
                        <div class="row g-2 mb-3">
                            <div class="col-md-6">
                                <div class="card h-100 followup-role-action-card">
                                    <div class="card-header"><span class="role-action-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none"><path d="M9 5h6M9 9h6M9 13h3m4-10h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h2m6 14 2 2 4-5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg></span><h6 class="mb-0">Summary QA</h6></div>
                                    <div class="card-body p-2">
                                        <form method="POST" action="{{ route('qa.tindak-lanjut') }}" enctype="multipart/form-data">
                                            @csrf
                                            <input type="hidden" name="nomor_tiket" value="{{ $ticket->nomor_tiket }}">
                                            @if($ticket->status !== 'resolved')
                                                <input type="hidden" name="status" value="resolved">
                                            @endif
                                            <div class="mb-1">
                                                <label class="small mb-1">Summary QA</label>
                                                <textarea name="qa_summary" class="form-control form-control-sm" rows="3" placeholder="Ringkasan..." {{ ($functions_disabled || $ticket->status === 'resolved') ? 'readonly' : '' }}>{{ old('qa_summary', $ticket->qa_summary ?? '') }}</textarea>
                                            </div>
                                            <div class="mb-2">
                                                <label class="small mb-1" for="qa-attachment">Lampiran QA (opsional)</label>
                                                <input type="file" name="qa_attachment" id="qa-attachment" class="form-control form-control-sm" {{ ($functions_disabled || $ticket->status === 'resolved') ? 'disabled' : '' }}>
                                                @if(!empty($ticket->qa_attachment))
                                                    <a href="{{ asset('storage/'.$ticket->qa_attachment) }}" target="_blank" rel="noopener" class="small d-inline-block mt-1">{{ \Illuminate\Support\Str::afterLast($ticket->qa_attachment, '/') }}</a>
                                                @endif
                                                @error('qa_attachment')<div class="small text-danger mt-1">{{ $message }}</div>@enderror
                                            </div>
                                            <div class="d-flex justify-content-end">
                                                <button type="submit" class="btn btn-sm btn-outline-primary" {{ ($functions_disabled || $ticket->status === 'resolved') ? 'disabled' : '' }}>Simpan</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card h-100 followup-role-action-card">
                                    <div class="card-header"><span class="role-action-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none"><path d="M9 5h6m-6 4h6m-8 6 2.5 2.5L17 10m-1-7h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h2" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg></span><h6 class="mb-0">Update Status Tiket</h6></div>
                                    <div class="card-body p-2">
                                        <form method="POST" action="{{ route('qa.tindak-lanjut') }}">
                                            @csrf
                                            <input type="hidden" name="nomor_tiket" value="{{ $ticket->nomor_tiket }}">
                                            <label class="small mb-1 d-block">Update Status</label>
                                            <div class="d-flex gap-2">
                                                <select name="status" class="form-select form-select-sm" aria-label="Status tiket QA" style="min-width:130px;" {{ $functions_disabled ? 'disabled' : '' }}>
                                                    <option value="on_progress" {{ $ticket->status=='in_progress' ? 'selected' : '' }}>On-Progress</option>
                                                    <option value="resolved" {{ $ticket->status === 'resolved' ? 'selected disabled' : ((isset($canResolve) && $canResolve) ? '' : 'disabled') }}>Resolved</option>
                                                </select>
                                                <button type="submit" class="btn btn-sm btn-outline-success" {{ $functions_disabled ? 'disabled' : '' }}>Update</button>
                                            </div>
                                            @if(session('error'))
                                                <div class="mt-1 small text-danger">{{ session('error') }}</div>
                                            @endif
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{-- end QA blocks --}}
                     @endif
                </div>
            </div>
        </div>
        <div class="col-12 col-xl-4 d-flex flex-column" style="gap: 1rem;">
            @if(!empty($ticket))
                @include('admin.partials.tindak-lanjut-summary', ['ticket' => $ticket])
            @endif
            <div class="card followup-chat-card h-100 flex-fill" id="chat-card">
                <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">Chat Tindak Lanjut</h6>
                </div>
                {{-- make chat-body flex column so messages fill available space and controls stay below --}}
                <div class="card-body p-2 d-flex flex-column" style="height:100%; min-height:0; box-sizing:border-box;">
                    @if(!empty($ticket))
                        <div id="chat-messages-wrapper" class="mb-2" style="flex:1 1 auto; min-height:0;">
                            <div id="chat-messages" class="h-100 overflow-auto" style="height:100%; padding:8px; background:#f8f9fa; border-radius:8px; display:flex;flex-direction:column;gap:8px; box-sizing:border-box;"></div>
                        </div>
                        <div class="chat-controls mt-2 pt-2 border-top" style="flex:0 0 auto;">
                            <div class="d-flex align-items-center" style="gap:.5rem;">
                                    <button id="btn-attach" type="button" class="btn btn-outline-secondary chat-icon-button" title="Lampiran" aria-label="Lampiran">
                                        <svg style="width:16px;height:16px;display:block;margin:auto;" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M6.354 1.5a3.5 3.5 0 0 1 4.95 4.95l-5.657 5.657a2.5 2.5 0 1 1-3.536-3.536l6.364-6.364" stroke="#6c757d" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                                            <path d="M9.95 3.9l-6.364 6.364a1.5 1.5 0 0 0 2.121 2.121l5.657-5.657" stroke="#6c757d" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </button>
                                    <input type="text" id="chat-input" class="form-control" placeholder="Ketik pesan..." aria-label="Chat input" style="height:36px;border-radius:8px;font-size:0.8rem;padding-top:0.35rem;padding-bottom:0.35rem; min-width:0; flex:1 1 auto;">
                                    <button id="btn-send" type="button" class="btn btn-primary chat-icon-button" title="Kirim" aria-label="Kirim">
                                        <svg style="width:16px;height:16px;display:block;margin:auto;" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M15.854.146a.5.5 0 0 0-.525-.116l-15 6a.5.5 0 0 0 .03.95l6.067 2.023L8.45 14.97a.5.5 0 0 0 .95.03l6-15a.5.5 0 0 0-.116-.525z" fill="#fff"/>
                                        </svg>
                                    </button>
                                </div>
                            <input type="file" id="chat-file" class="d-none">
                            <div id="chat-attachment-preview" class="mt-2 small text-muted"></div>
                        </div>
                    @else
                        <div class="chat-role-empty"><i class="far fa-comments"></i><strong>Belum ada tiket dipilih</strong><span>Pilih tiket dari Daftar Tiket untuk mulai percakapan</span></div>
                    @endif
                </div>
            </div>
         </div>
    </div>
@push('dashboard')
<script>
document.addEventListener('DOMContentLoaded', function(){
    var btn = document.getElementById('qa-btn-show-more-cifs');
    if(!btn) return;
    btn.addEventListener('click', function(){
        var more = document.getElementById('qa-more-cifs');
        if(!more) return;
        more.classList.remove('d-none');
        btn.style.display = 'none';
    });
});
</script>
@endpush
</div>

@if(!empty($ticket))
    @include('partials.ticket-history-modal', ['ticket' => $ticket])
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
        if (!msgs.length) {
            messagesEl.innerHTML = '<div class="chat-role-empty"><i class="far fa-comments"></i><strong>Belum ada pesan</strong><span>Mulai percakapan untuk tindak lanjut tiket ini</span></div>';
            return;
        }
        msgs.forEach(m => {
            const wrap = document.createElement('div');
            wrap.style.display = 'flex';
            wrap.style.flexDirection = 'column';
            wrap.style.maxWidth = '85%';
            wrap.className = (m.user_id === @json(auth()->id())) ? 'align-self-end' : 'align-self-start';

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
                bubble.style.background = '#5b3fd3';
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

<script>
document.addEventListener('DOMContentLoaded', function(){
    const flipQa = document.getElementById('ticket-flip-qa');
    if (!flipQa) return;
    const isTextSelected = () => {
        try {
            const s = window.getSelection ? window.getSelection().toString() : (document.selection && document.selection.createRange().text) || '';
            return !!(s && s.length);
        } catch(e) { return false; }
    };
    flipQa.addEventListener('click', (ev)=> {
        if (isTextSelected()) return;
        flipQa.classList.toggle('flipped');
    });
    flipQa.addEventListener('keydown', (e)=> {
        if (e.key === 'Enter' || e.key === ' ') {
            if (isTextSelected()) return;
            e.preventDefault();
            flipQa.classList.toggle('flipped');
        }
    });

    // ensure flip-card height matches front content to avoid cutting the next cards
    const front = flipQa.querySelector('.flip-card-front');
    const inner = flipQa.querySelector('.flip-card-inner');
    const adjustHeight = () => {
        if (!front || !flipQa) return;
        const maxCompactHeight = 320;
        const h = Math.min(front.scrollHeight, maxCompactHeight);
        flipQa.style.minHeight = h + 'px';
        if (inner) inner.style.minHeight = h + 'px';
    };
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
<script>
document.addEventListener('DOMContentLoaded', function(){
    const f = document.querySelector('footer');
    if (f) f.remove();
});
</script>
@endsection
