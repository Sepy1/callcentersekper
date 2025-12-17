@extends('layouts.user_type.auth')

@section('content')
<div class="container-fluid py-2">
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

    <div class="row" style="min-height: calc(100vh - 100px);">
        <div class="col-lg-9 d-flex flex-column" style="gap: 1.5rem;">
            <div class="card flex-fill h-100 mb-3">
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
                        <div id="detail-tiket-card" class="mb-3">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="flip-card" id="ticket-flip-qa" aria-pressed="false" role="button" tabindex="0" title="Klik untuk melihat informasi pelapor">
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
                                    <div class="card h-100">
                                        <div class="card-header pb-0">
                                            <h6 class="mb-0">Officer Tertugaskan (TL & Lampiran)</h6>
                                        </div>
                                        <div class="card-body p-2">
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
                                <div class="card h-100">
                                    <div class="card-body p-2">
                                        <form method="POST" action="{{ route('qa.tindak-lanjut') }}">
                                            @csrf
                                            <input type="hidden" name="nomor_tiket" value="{{ $ticket->nomor_tiket }}">
                                            <div class="mb-1">
                                                <label class="small mb-1">Summary QA</label>
                                                <textarea name="qa_summary" class="form-control form-control-sm" rows="3" placeholder="Ringkasan..." {{ $functions_disabled ? 'readonly' : '' }}>{{ old('qa_summary', $ticket->qa_summary ?? '') }}</textarea>
                                            </div>
                                            <div class="d-flex justify-content-end">
                                                <button type="submit" class="btn btn-sm btn-outline-primary" {{ $functions_disabled ? 'disabled' : '' }}>Simpan</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card h-100">
                                    <div class="card-body p-2">
                                        <form method="POST" action="{{ route('qa.tindak-lanjut') }}">
                                            @csrf
                                            <input type="hidden" name="nomor_tiket" value="{{ $ticket->nomor_tiket }}">
                                            <label class="small mb-1 d-block">Update Status</label>
                                            <div class="d-flex gap-2">
                                                <select name="status" class="form-select form-select-sm" style="min-width:130px;" {{ $functions_disabled ? 'disabled' : '' }}>
                                                    <option value="on_progress" {{ $ticket->status=='in_progress' ? 'selected' : '' }}>On-Progress</option>
                                                    <option value="resolved" {{ (isset($canResolve) && $canResolve) ? '' : 'disabled' }}>Resolved</option>
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
        <div class="col-lg-3 d-flex flex-column" style="gap: 1.5rem;">
            <div class="card flex-fill h-100" id="chat-card">
                <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">Chat Tindak Lanjut</h6>
                </div>
                {{-- make chat-body flex column so messages fill available space and controls stay below --}}
                <div class="card-body p-2 d-flex flex-column" style="height:520px; min-height:420px; box-sizing:border-box;">
                    @if(!empty($ticket))
                        <div id="chat-messages-wrapper" class="mb-2" style="flex:1 1 auto; min-height:0;">
                            <div id="chat-messages" class="h-100 overflow-auto" style="height:100%; padding:8px; background:#f8f9fa; border-radius:8px; display:flex;flex-direction:column;gap:8px; box-sizing:border-box;"></div>
                        </div>
                        <div class="chat-controls mt-2" style="flex:0 0 auto;">
                            <div class="card">
                                <div class="card-body py-2 d-flex align-items-center" style="gap:.5rem;">
                                    <button id="btn-attach" type="button" class="btn btn-outline-secondary d-inline-flex align-items-center justify-content-center" title="Lampiran" aria-label="Lampiran" style="width:44px;height:44px;padding:0;border-radius:8px;flex:0 0 44px;">
                                        <svg style="width:20px;height:20px;display:block;margin:auto;" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M6.354 1.5a3.5 3.5 0 0 1 4.95 4.95l-5.657 5.657a2.5 2.5 0 1 1-3.536-3.536l6.364-6.364" stroke="#6c757d" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                                            <path d="M9.95 3.9l-6.364 6.364a1.5 1.5 0 0 0 2.121 2.121l5.657-5.657" stroke="#6c757d" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </button>
                                    <input type="text" id="chat-input" class="form-control" placeholder="Ketik pesan..." aria-label="Chat input" style="height:44px;border-radius:8px; min-width:0; flex:1 1 auto;">
                                    <button id="btn-send" type="button" class="btn btn-primary d-inline-flex align-items-center justify-content-center" title="Kirim" aria-label="Kirim" style="width:44px;height:44px;padding:0;border-radius:8px;flex:0 0 44px;">
                                        <svg style="width:20px;height:20px;display:block;margin:auto;" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M15.854.146a.5.5 0 0 0-.525-.116l-15 6a.5.5 0 0 0 .03.95l6.067 2.023L8.45 14.97a.5.5 0 0 0 .95.03l6-15a.5.5 0 0 0-.116-.525z" fill="#fff"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                            <input type="file" id="chat-file" class="d-none">
                            <div id="chat-attachment-preview" class="mt-2 small"></div>
                        </div>
                    @else
                        <div class="text-center text-muted">Cari tiket untuk mulai chat.</div>
                    @endif
                </div>
            </div>
         </div>
    </div>
</div>

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
        const h = front.scrollHeight;
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
<script>
(function(){
    function fitContainer() {
        const container = document.querySelector('.container-fluid');
        if (!container) return;
        container.style.transformOrigin = 'top center';
        container.style.transition = 'transform 160ms ease';
        container.style.transform = 'none';
        requestAnimationFrame(()=> {
            const cw = container.scrollWidth, ch = container.scrollHeight;
            const vw = window.innerWidth, vh = window.innerHeight;
            const scale = Math.min(1, vw / cw, vh / ch);
            container.style.transform = 'scale(' + scale + ')';
            document.documentElement.style.overflow = 'hidden';
            document.body.style.overflow = 'hidden';
        });
    }
    window.addEventListener('resize', fitContainer);
    window.addEventListener('orientationchange', fitContainer);
    document.addEventListener('DOMContentLoaded', fitContainer);
    setTimeout(fitContainer, 300);
})();
</script>
<script>
document.addEventListener('DOMContentLoaded', function(){
	const username = {!! json_encode(auth()->user()->name ?? '') !!};
	if (!username) return;
	const sidebar = document.querySelector('#sidenav-main') || document.querySelector('.sidenav') || document.querySelector('.navbar-vertical') || document.querySelector('.sidebar');
	if (!sidebar || sidebar.querySelector('.sidebar-user')) return;
	const initials = username.split(' ').map(n=>n[0]).slice(0,2).join('').toUpperCase();
	const header = document.createElement('div');
	header.className = 'sidebar-user px-3 py-2 border-bottom';
	header.innerHTML = '<div class="d-flex align-items-center"><div class="avatar avatar-sm bg-gradient-primary text-white rounded-circle me-2">'+initials+'</div><div><div class="fw-bold small">'+username+'</div><a href="/logout" class="small text-decoration-none">Logout</a></div></div>';
	const ref = sidebar.querySelector('.nav') || sidebar.querySelector('ul') || sidebar.firstChild;
	sidebar.insertBefore(header, ref);
});
</script>
@endsection
