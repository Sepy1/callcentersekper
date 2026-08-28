@extends('layouts.user_type.auth')

@section('content')
<div class="container-fluid py-2 followup-page">
	<style>
		/* flip-card and chat layout */
		.flip-card { perspective: 1000px; position: relative; }
		.flip-card .flip-card-inner {
			position: relative;
			width: 100%;
			transition: transform 0.6s;
			transform-style: preserve-3d;
			-webkit-transform-style: preserve-3d;
		}
		.flip-card.flipped .flip-card-inner {
			transform: rotateY(180deg);
			-webkit-transform: rotateY(180deg);
		}
		.flip-card-front, .flip-card-back {
			position: absolute;
			inset: 0;
			width: 100%;
			height: 100%;
			box-sizing: border-box;
			backface-visibility: hidden;
			-webkit-backface-visibility: hidden;
			background: transparent !important;
		}
		.flip-card-front { z-index: 2; }
		.flip-card.flipped .flip-card-front { visibility: hidden; }
		.flip-card-back { transform: rotateY(180deg); -webkit-transform: rotateY(180deg); padding: 1rem; }
		.flip-card .flip-card-front *, .flip-card .flip-card-back * { backface-visibility: hidden; -webkit-backface-visibility: hidden; }

		/* chat layout */
		#chat-card, #chat-card .card { overflow: visible; }
		#chat-card .card-body { box-sizing: border-box; }
		#chat-card .card .card-body.d-flex { gap: .5rem; }
		#chat-card .card .card-body .form-control { min-width: 0; flex: 1 1 auto; }
		#chat-card .card .card-body .btn { flex: 0 0 44px; width:44px; height:44px; padding:0; }
		#chat-messages { box-sizing: border-box; }

		/* ensure vertical gap between stacked rows inside detail-tiket-card */
		#detail-tiket-card .row + .row {
			margin-top: 1.5rem; /* same vertical gap used elsewhere */
		}

		/* keep internal card margins normalized so gaps don't double */
		#detail-tiket-card .card { margin-bottom: 0; }

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

	{{-- Toast --}}
	@if(session('success') || session('error'))
	<div class="position-fixed bottom-0 end-0 p-3" style="z-index:1080;">
		<div id="toast-notif" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
			<div class="toast-header bg-{{ session('success') ? 'success' : 'danger' }} text-white">
				<strong class="me-auto">{{ session('success') ? 'Sukses' : 'Error' }}</strong>
				<button type="button" class="btn-close btn-close-white ms-2 mb-1" data-bs-dismiss="toast" aria-label="Close"></button>
			</div>
			<div class="toast-body bg-white">
				{{ session('success') ?? session('error') }}
			</div>
		</div>
	</div>
	@endif

	<div class="row followup-layout-row">
		<div class="col-12 col-xl-8 d-flex flex-column" style="gap:1rem;">
			<div class="card followup-main-card h-100 flex-fill">
				<div class="card-header pb-0"><h6 class="mb-0">Tindak Lanjut Tiket</h6></div>
				<div class="card-body d-flex flex-column" style="gap:1.5rem;">
					<form id="filter-tiket-form" class="d-flex gap-2 align-items-center mb-4" method="GET" action="{{ url('officer/tindak-lanjut') }}">
						<div class="input-group input-group-sm border" style="max-width:300px;">
							<span class="input-group-text bg-white border-0"><i class="fas fa-search text-secondary"></i></span>
							<input type="text" id="nomor_tiket" name="nomor_tiket" value="{{ request('nomor_tiket') }}" class="form-control border-0" placeholder="Cari nomor tiket...">
						</div>
						<button type="submit" class="btn btn-sm bg-gradient-primary rounded-pill px-3">Cari</button>
						@if(request('nomor_tiket')) <a href="{{ url('officer/tindak-lanjut') }}" class="btn btn-sm bg-gradient-secondary rounded-pill px-3">Reset</a> @endif
					</form>

					@php
						$assigned = false;
						$pivot = null;
						if (!empty($ticket)) {
							$user = auth()->user();
							$pivot = \DB::table('ticket_officer')->where('ticket_id', $ticket->id)->where('officer_id', $user->id)->first();
							$assigned = $pivot ? true : false;
						}
                        $functions_disabled = !empty($ticket) && in_array($ticket->status, ['closed', 'rejected']);
					@endphp

					@if(!request('nomor_tiket') && !request('ticket_id'))
						<div class="text-muted small">Masukkan nomor tiket untuk mulai tindak lanjut.</div>
					@elseif(!$ticket)
						<div class="card"><div class="card-body text-center text-danger">Tiket tidak ditemukan.</div></div>
					@elseif(!$assigned)
						<div class="card"><div class="card-body text-center text-danger">Anda Tidak Ditugaskan Untuk Mengelola Tiket Ini</div></div>
					@else
						<div id="detail-tiket-card" class="mb-2">
							<div class="row g-3 detail-top-row">
								<div class="col-md-6">
									<div class="flip-card detail-ticket-compact" id="ticket-flip-officer" role="button" tabindex="0" aria-label="Klik untuk melihat informasi pelapor">
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
																<span id="officer-more-cifs" class="d-none">
																	@foreach($hiddenCifs as $c)
																		<span class="badge bg-info text-dark me-1">{{ $c }}</span>
																	@endforeach
																</span>
																<button type="button" id="officer-btn-show-more-cifs" class="btn btn-sm btn-link">+{{ count($hiddenCifs) }} more</button>
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
										<div class="card-header pb-0"><h6 class="mb-0">Officer Tertugaskan (TL & Status)</h6></div>
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
												$assignedOfficers = \DB::table('ticket_officer')->where('ticket_id', $ticket->id)->orderBy('created_at')->get();
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
										</div>
									</div>
								</div>
							</div>
						</div>

						<div class="row followup-role-action-row" style="gap:1.5rem 0;">
								<div class="col-md-6 mb-3">
									<div class="card flex-fill followup-role-action-card" style="max-height:360px; display:flex; flex-direction:column;">
										<div class="card-header pb-0"><span class="role-action-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none"><path d="m4 20 4.2-1 10.7-10.7a2.1 2.1 0 0 0-3-3L5.2 16 4 20Zm10.5-13.3 3 3" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg></span><h6 class="mb-0">Isian Tindak Lanjut</h6></div>
										<div class="card-body overflow-auto" style="flex:1 1 auto; min-height:0;">
											<form method="POST" action="{{ route('officer.tindak-lanjut.proses') }}" enctype="multipart/form-data" autocomplete="off" id="tindak-form">
												@csrf
												<input type="hidden" name="nomor_tiket" value="{{ $ticket->nomor_tiket }}">
												<input type="hidden" name="form_action" value="save_followup">
												<div class="row g-3 align-items-stretch officer-followup-form-row">
													<div class="col-12 col-lg-7">
														<textarea class="form-control h-100" name="tindak_lanjut" rows="4" aria-label="Tulis tindak lanjut" placeholder="Tulis tindak lanjut di sini..." {{ ($functions_disabled || ($pivot && $pivot->status === 'proses_qa')) ? 'readonly' : '' }}>{{ $pivot ? $pivot->tl : '' }}</textarea>
													</div>
													<div class="col-12 col-lg-5 d-flex flex-column officer-followup-side-column">
														<div>
															<label class="form-label small mb-1" for="tindak-lanjut-lampiran">Lampiran (opsional)</label>
															<input type="file" name="lampiran" id="tindak-lanjut-lampiran" class="form-control form-control-sm" onchange="document.getElementById('tindak-lanjut-lampiran-preview').innerText = this.files && this.files[0] ? this.files[0].name : '';" {{ ($functions_disabled || ($pivot && $pivot->status === 'proses_qa')) ? 'disabled' : '' }}>
															<div id="tindak-lanjut-lampiran-preview" class="small text-muted mt-1"></div>
														</div>
														<button type="submit" class="btn btn-sm btn-outline-primary w-100 mt-auto mb-0" {{ ($functions_disabled || ($pivot && $pivot->status === 'proses_qa')) ? 'disabled' : '' }}>Simpan Tindak Lanjut</button>
													</div>
												</div>
											</form>
										</div>
									</div>
								</div>

								<div class="col-md-6 mb-3">
									<div class="card flex-fill h-100 followup-role-action-card">
										<div class="card-header pb-0"><span class="role-action-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none"><path d="M9 5h6m-6 4h6m-8 6 2.5 2.5L17 10m-1-7h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h2" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg></span><h6 class="mb-0">Update Status Tiket</h6></div>
										<div class="card-body">
											<form method="POST" action="{{ route('officer.tindak-lanjut.proses') }}" class="d-flex flex-wrap gap-2 align-items-center" id="status-form">
												@csrf
												<input type="hidden" name="nomor_tiket" value="{{ $ticket->nomor_tiket }}">
												<label class="mb-0 fw-bold">Status:</label>
												<select class="form-select form-select-sm" name="status" aria-label="Status officer" style="min-width:140px;" {{ $functions_disabled ? 'disabled' : '' }}>
													<option value="proses_qa" {{ ($pivot && $pivot->status == 'proses_qa') ? 'selected disabled' : '' }}>Proses QA</option>
													<option value="cancel_qa" {{ ($pivot && $pivot->status == 'cancel_qa') ? 'selected' : '' }}>Cancel QA</option>
												</select>
												<button type="submit" class="btn btn-sm btn-outline-success" {{ $functions_disabled ? 'disabled' : '' }}>Update Status</button>
											</form>
										</div>
									</div>
								</div>
						</div>
					@endif

				</div>

@push('dashboard')
<script>
document.addEventListener('DOMContentLoaded', function(){
	var btn = document.getElementById('officer-btn-show-more-cifs');
	if(!btn) return;
	btn.addEventListener('click', function(){
		var more = document.getElementById('officer-more-cifs');
		if(!more) return;
		more.classList.remove('d-none');
		btn.style.display = 'none';
	});
});
</script>
@endpush
			</div>
		</div>

		<div class="col-12 col-xl-4 d-flex flex-column" style="gap:1rem;">
			@if(!empty($ticket) && isset($assigned) && $assigned)
				@include('admin.partials.tindak-lanjut-summary', ['ticket' => $ticket])
			@endif
			<div class="card followup-chat-card h-100 flex-fill" id="chat-card">
				<div class="card-header pb-0 d-flex justify-content-between align-items-center"><h6 class="mb-0">Chat Tindak Lanjut</h6></div>
				<div class="card-body p-2 d-flex flex-column" style="height:100%; box-sizing:border-box;">
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
</div>

@if(!empty($ticket) && isset($assigned) && $assigned)
	@include('partials.ticket-history-modal', ['ticket' => $ticket])
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
		try {
			const res = await fetch("{{ url('/chat/messages') }}/" + encodeURIComponent(nomor), { credentials:'same-origin', headers:{'Accept':'application/json'} });
			if(!res.ok) return;
			const msgs = await res.json();
			render(msgs);
		} catch(e){
			console.error('fetchMessages error', e);
		}
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
		preview.dataset.file = '';
	});

	btnSend && btnSend.addEventListener('click', async ()=>{
		const text = (input.value||'').trim();
		const f = fileInput.files[0];
		if(!text && !f) return;
		const fd = new FormData();
		fd.append('nomor_tiket', nomor);
		fd.append('message', text);
		if(f) fd.append('attachment', f);
		try {
			const res = await fetch("{{ route('chat.send') }}", {
				method: 'POST',
				credentials: 'same-origin',
				headers: {
					'X-CSRF-TOKEN': token,
					'Accept': 'application/json',
					'X-CHAT-SEND': '1'
				},
				body: fd
			});
			if(res.ok){
				input.value = '';
				fileInput.value = '';
				preview.innerHTML = '';
				await fetchMessages();
			} else {
				let err = 'Gagal mengirim pesan';
				try { const j = await res.json(); if(j && j.message) err = j.message; } catch(e){}
				alert(err);
			}
		} catch(e){
			console.error('send error', e);
			alert('Gagal mengirim pesan (network error)');
		}
	});

	// initial + polling
	fetchMessages();
	setInterval(fetchMessages, 5000);

	// flip toggle & height adjust (prevent flip while selecting text)
	const flip = document.getElementById('ticket-flip-officer');
	if (flip) {
		const front = flip.querySelector('.flip-card-front');
		const inner = flip.querySelector('.flip-card-inner');
		const isTextSelected = ()=> {
			try { return !!(window.getSelection ? window.getSelection().toString() : (document.selection && document.selection.createRange().text)); } catch(e){ return false; }
		};
		const adjustHeight = ()=> {
			if (!front) return;
			const maxCompactHeight = 320;
			const h = Math.min(front.scrollHeight, maxCompactHeight);
			flip.style.minHeight = h + 'px';
			if (inner) inner.style.minHeight = h + 'px';
		};
		flip.addEventListener('click', (ev)=> { if (isTextSelected()) return; flip.classList.toggle('flipped'); });
		flip.addEventListener('keydown', (e)=> { if ((e.key==='Enter'||e.key===' ') && !isTextSelected()) { e.preventDefault(); flip.classList.toggle('flipped'); } });
		adjustHeight();
		window.addEventListener('load', adjustHeight);
		window.addEventListener('resize', adjustHeight);
		if (window.ResizeObserver) {
			try { new ResizeObserver(adjustHeight).observe(front); } catch(e){}
		}
	}
})();
</script>
@endif

@if(session('success') || session('error'))
<script>
document.addEventListener('DOMContentLoaded', function(){
	var toastEl = document.getElementById('toast-notif');
	if (toastEl) {
		var toast = new bootstrap.Toast(toastEl, { delay: 4000 });
		toast.show();
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
@endsection
