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

@media (min-width: 1200px) {
    .followup-chat-card {
        position: sticky;
        top: 1rem;
    }
}

/* Enterprise helpdesk redesign */
:root { --hd-primary:#5b3fd3; --hd-primary-dark:#4327b9; --hd-navy:#17213b; --hd-bg:#f7f8fc; --hd-border:#e7e9f0; --hd-muted:#77809a; --hd-success:#36b765; }
body { background:var(--app-background,var(--hd-bg)) !important; color:#202840; }
.followup-page { max-width:1800px; margin:0 auto; padding:0 1rem 1.5rem !important; }
.helpdesk-page-header { display:flex; align-items:center; justify-content:space-between; gap:1rem; margin:-1rem -1rem 1.25rem; padding:1.15rem 1.25rem; background:#fff; border-bottom:1px solid var(--hd-border); }
.helpdesk-page-title { display:flex; align-items:center; gap:.9rem; }
.helpdesk-menu-button { width:42px; height:42px; border:1px solid var(--hd-border); border-radius:10px; background:#fff; color:#313950; box-shadow:0 3px 12px rgba(27,37,63,.05); }
.helpdesk-page-title h1 { font-size:1.45rem; margin:0 0 .2rem; color:#171d34; font-weight:700; }
.helpdesk-page-title p { margin:0; color:var(--hd-muted); font-size:.86rem; }
.helpdesk-user { display:flex; align-items:center; gap:.7rem; }
.helpdesk-avatar { width:40px; height:40px; border-radius:50%; display:grid; place-items:center; background:var(--hd-primary); color:#fff; font-weight:700; }
.helpdesk-user strong,.helpdesk-user small { display:block; line-height:1.25; }.helpdesk-user small{color:var(--hd-muted)}
.followup-layout-row { --bs-gutter-x:1.25rem; }
.followup-main-card { background:transparent; box-shadow:none; }
.followup-main-card>.card-header { display:none; }
.followup-main-card>.card-body { padding:0; gap:1rem !important; }
.detail-top-row>div,.followup-action-row>div { display:flex; }
.detail-ticket-compact { width:100%; height:450px; min-height:450px; }
.detail-ticket-compact { border-radius:15px; box-shadow:0 10px 28px rgba(20,29,54,.14); }
.detail-ticket-compact .flip-card-front>.card { border:0; background:transparent !important; box-shadow:none; }
.ticket-overview { min-height:450px; padding:1.35rem; border-radius:15px; color:#fff; background:linear-gradient(145deg,#20285d 0%,#142039 100%); }
.ticket-overview__page-heading { margin:-.15rem 0 1.1rem; padding-bottom:1rem; border-bottom:1px solid rgba(255,255,255,.1); }
.ticket-overview__page-heading h1 { margin:0 0 .25rem; color:#fff; font-size:1.25rem; font-weight:700; }
.ticket-overview__page-heading p { margin:0; color:#aeb7d8; font-size:.75rem; }
.ticket-overview__header { display:flex; align-items:flex-start; justify-content:space-between; gap:1rem; margin-bottom:1.25rem; }
.ticket-overview__actions { display:flex; align-items:center; gap:.55rem; }
.ticket-history-button { display:inline-flex; align-items:center; gap:.4rem; padding:.38rem .65rem; border:1px solid rgba(255,255,255,.24); border-radius:7px; background:rgba(255,255,255,.08); color:#fff; font-size:.65rem; font-weight:700; text-transform:uppercase; letter-spacing:.03em; transition:.18s ease; }
.ticket-history-button:hover,.ticket-history-button:focus { background:#fff; color:var(--hd-primary); outline:none; }
.ticket-overview__eyebrow { display:block; color:#aeb7d8; font-size:.7rem; text-transform:uppercase; letter-spacing:.08em; margin-bottom:.35rem; }
.ticket-overview h2 { color:#fff; font-size:1.25rem; margin:0; }.status-pill { display:inline-flex; padding:.38rem .65rem; border-radius:7px; font-size:.65rem; font-weight:800; text-transform:uppercase; letter-spacing:.04em; background:#65708d; color:#fff; }
.status-pill--open,.status-pill--in_progress { background:var(--hd-success); }.status-pill--rejected{background:#ef4444}
.ticket-overview__grid { display:grid; grid-template-columns:1.15fr 1.45fr 1.15fr 1fr .8fr; gap:1rem; }
.ticket-field span:not(.officer-name) { display:block; color:#aeb7d8; font-size:.7rem; margin-bottom:.25rem; }.ticket-field strong { display:block; color:#fff; font-size:.82rem; line-height:1.45; font-weight:600; }
.ticket-field--wide { margin-bottom:.58rem; }.ticket-overview__divider { height:1px; margin:.9rem 0; background:rgba(255,255,255,.1); }.ticket-whatsapp{color:#48d67a;margin-left:.35rem}.officer-name{display:inline-block;margin:0 .3rem .2rem 0}.officer-name.is-complete{color:#69dd8e}
.ticket-overview__hint { color:#929dbc; font-size:.65rem; margin-top:.8rem; }.ticket-overview__hint i{margin-right:.35rem}
.flip-card-back { padding:1.35rem; border:1px solid var(--hd-border); border-radius:15px; background:#fff !important; color:#30384e; box-shadow:0 8px 24px rgba(21,31,53,.08); }
.followup-action-card,.followup-chat-card { border:1px solid var(--hd-border); border-radius:15px; box-shadow:0 6px 22px rgba(26,33,56,.05); overflow:hidden; }
.followup-action-card>.card-header,.followup-chat-card>.card-header { padding:1.15rem 1.25rem; border-bottom:1px solid var(--hd-border); background:#fff; }
.followup-action-card>.card-header h6,.followup-chat-card>.card-header h6 { color:#28356b; font-size:.95rem; }
.qa-panel,.closing-panel { border:0 !important; border-radius:10px !important; box-shadow:none !important; }.qa-panel{background:#ef7d20 !important}.closing-panel{background:#172235 !important}
.followup-action-row { row-gap:1rem !important; }.followup-action-row>div{margin-bottom:0 !important}.followup-action-card>.card-body{padding:1rem 1.25rem}
.section-title-icon { width:30px;height:30px;border-radius:9px;display:inline-grid;place-items:center;background:#eeeaff;color:var(--hd-primary);margin-right:.55rem; }
.section-subtitle{display:block;color:var(--hd-muted);font-size:.7rem;font-weight:400;margin:.2rem 0 0 2.7rem}
.followup-action-card label { color:#46506b; font-size:.75rem; }.followup-action-card .form-control,.followup-action-card .form-select { min-height:43px;border-color:var(--hd-border);border-radius:9px;box-shadow:none}.followup-action-card .form-control:focus,.followup-action-card .form-select:focus{border-color:var(--hd-primary);box-shadow:0 0 0 3px rgba(91,63,211,.1)}
.action-helper { color:var(--hd-muted);font-size:.7rem;margin:.4rem 0 .8rem}.btn-helpdesk-primary,.btn-helpdesk-success{border:0!important;border-radius:8px!important;color:#fff!important;font-weight:700;padding:.65rem 1.1rem!important;margin:0!important}.btn-helpdesk-primary{background:var(--hd-primary)!important}.btn-helpdesk-success{background:var(--hd-success)!important}
.current-status-row{display:flex;align-items:center;gap:.6rem;margin-bottom:.8rem;color:#556078;font-size:.75rem}
.followup-chat-card { min-height:0; background:#fff; }.chat-online{padding:.35rem .65rem;border-radius:99px;background:#e4f8e9;color:#26934c;font-size:.68rem;font-weight:700}.followup-chat-card>.card-body{padding:0!important}.chat-message-surface{background:#fbfbfd!important;border-radius:0!important;padding:1rem!important}.chat-empty-state{margin:auto;text-align:center;padding:2rem;color:var(--hd-muted)}.chat-empty-icon{width:82px;height:82px;border-radius:50%;display:grid;place-items:center;margin:0 auto 1rem;background:#f0edff;color:var(--hd-primary);font-size:2rem}.chat-empty-state strong{display:block;color:#20263b;font-size:1rem;margin-bottom:.35rem}.chat-controls{padding:.9rem 1rem!important;margin:0!important;background:#fff}.chat-controls .form-control{height:44px!important;border-color:var(--hd-border);font-size:.82rem!important}.chat-controls .btn{width:44px!important;height:44px!important;margin:0!important}.chat-controls #btn-send{background:var(--hd-primary);border-color:var(--hd-primary)}
.chat-ticket-summary { flex:0 0 auto; margin:0; border:1px solid var(--hd-border); border-radius:15px; background:#f8f9fc; box-shadow:0 6px 22px rgba(26,33,56,.05); overflow:hidden; }
.chat-ticket-summary__header { display:flex; align-items:center; justify-content:space-between; gap:.75rem; padding:.85rem .9rem; background:#fff; border-bottom:1px solid var(--hd-border); }
.chat-ticket-summary__eyebrow { display:block; margin-bottom:.15rem; color:var(--hd-primary); font-size:.62rem; font-weight:800; text-transform:uppercase; letter-spacing:.07em; }
.chat-ticket-summary__header h6 { margin:0; color:#28356b; font-size:.82rem; }
.chat-ticket-summary__body { max-height:285px; padding:.75rem; overflow-y:auto; }
.chat-ticket-summary .qa-panel,.chat-ticket-summary .closing-panel { color:#fff; }
.chat-ticket-summary__officers { display:flex; flex-direction:column; gap:.55rem; }
.officer-summary-item { padding:.7rem; border:1px solid var(--hd-border); border-radius:9px; background:#fff; }
.officer-summary-item__top { display:flex; align-items:center; justify-content:space-between; gap:.5rem; color:#28324b; font-size:.74rem; }
.officer-summary-item__top .badge { font-size:.56rem; text-transform:capitalize; }
.officer-summary-item__tl { margin-top:.55rem; padding-top:.5rem; border-top:1px solid #eef0f5; }
.officer-summary-item__tl span { color:var(--hd-muted); font-size:.6rem; text-transform:uppercase; letter-spacing:.04em; }
.officer-summary-item__tl p { margin:.15rem 0 0; color:#4b556b; font-size:.7rem; line-height:1.45; }
.officer-summary-item__attachment { display:flex; align-items:center; gap:.45rem; margin-top:.55rem; padding:.5rem .6rem; border-radius:7px; background:#f0edff; color:var(--hd-primary); font-size:.68rem; text-decoration:none; }
.officer-summary-item__attachment span { min-width:0; flex:1; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
.officer-summary-empty { display:flex; align-items:center; justify-content:center; gap:.5rem; padding:.9rem; color:var(--hd-muted); font-size:.7rem; }
@media(min-width:1200px){
    html,body.helpdesk-followup { height:100%; overflow:hidden; }
    body.helpdesk-followup .main-content { height:100vh; margin-top:0!important; overflow:hidden; }
    body.helpdesk-followup .main-content>.container-fluid.py-4 { height:100%; padding:12px!important; overflow:hidden; }
    .followup-page { height:100%; padding:0!important; overflow:hidden; }
    .followup-layout-row { height:100%; margin-top:0; margin-bottom:0; overflow:hidden; }
    .followup-layout-row>.col-xl-8,.followup-layout-row>.col-xl-4 { height:100%; min-height:0; overflow:hidden; }
    .followup-main-card { height:100%!important; min-height:0; }
    .followup-main-card>.card-body { display:grid!important; grid-template-rows:minmax(0,1fr) auto; min-height:0; overflow:hidden; }
    #detail-tiket-card,.detail-top-row,.detail-top-row>div { height:100%; min-height:0; margin-bottom:0!important; }
    .detail-ticket-compact { height:100%!important; min-height:0!important; }
    .detail-ticket-compact .flip-card-inner { height:100%; min-height:0!important; }
    .followup-action-row { flex:0 0 auto; margin-top:0; }
    .followup-layout-row>.col-xl-4 { display:flex; flex-direction:column; gap:1rem!important; }
    .chat-ticket-summary { max-height:42%; min-height:0; }
    .chat-ticket-summary__body { max-height:calc(42vh - 70px); }
    .followup-chat-card { position:relative; top:auto; height:auto!important; min-height:0; flex:1 1 0!important; }
    #chat-messages-wrapper { min-height:0; overflow:hidden; }
}
@media(max-width:991.98px){.ticket-overview__grid{grid-template-columns:repeat(3,minmax(0,1fr))}}
@media(max-width:767.98px){.followup-page{padding-inline:.5rem!important}.ticket-overview__grid{grid-template-columns:1fr 1fr}.detail-ticket-compact{height:470px;min-height:470px}.followup-chat-card{min-height:620px}}
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
                    {{-- Card Detail Tiket: split into info (left) and officer list (right) --}}
                    @if(!empty($ticket))
                        @php $functions_disabled = in_array($ticket->status, ['closed','rejected']); @endphp
                        <div id="detail-tiket-card" class="mb-2">
                            <div class="row g-3 detail-top-row">
                                <div class="col-12">
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
                            </div>
                        </div>
                        {{-- follow-up row: assign officer form & update status (unchanged) --}}
                        <div class="row followup-action-row">
                            <div class="col-12 col-md-6 mb-3">
                                <div class="card flex-fill h-100 followup-action-card">
                                    <div class="card-header pb-0">
                                        <h6 class="mb-0"><span class="section-title-icon"><i class="fas fa-user-plus"></i></span>Assign Officer <small class="section-subtitle">Multi assign dengan mention</small></h6>
                                    </div>
                                    <div class="card-body">
                                        <form method="POST" action="{{ route('admin.tindak-lanjut') }}" id="assign-officer-form" class="mb-3" @if($functions_disabled) aria-disabled="true" @endif>
                                            @csrf
                                            <input type="hidden" name="nomor_tiket" value="{{ $ticket->nomor_tiket }}">
                                            <div class="mb-3">
                                                <label class="fw-bold mb-1" for="officer-mention">Assign ke Officer</label>
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
                                                <div class="action-helper">Gunakan @nama untuk mention officer lainnya</div>
                                            </div>
                                            <button type="submit" class="btn btn-sm btn-helpdesk-primary" {{ $functions_disabled ? 'disabled' : '' }}><i class="fas fa-user-plus me-1"></i> Assign Officer</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-md-6 mb-3">
                                <div class="card flex-fill h-100 followup-action-card">
                                    <div class="card-header pb-0">
                                        <h6 class="mb-0"><span class="section-title-icon"><i class="fas fa-clipboard-check"></i></span>Update Status Tiket</h6>
                                    </div>
                                    <div class="card-body">
                                        <form method="POST" action="{{ route('admin.tindak-lanjut') }}" id="admin-status-form" @if($functions_disabled) aria-disabled="true" @endif>
                                            @csrf
                                            <input type="hidden" name="nomor_tiket" value="{{ $ticket->nomor_tiket }}">
                                            <div class="current-status-row">Status Saat Ini <span class="status-pill status-pill--{{ $ticket->status }}">{{ str_replace('_', ' ', $ticket->status) }}</span></div>
                                            <label class="mb-1 fw-bold" for="admin-status-select">Ubah Status</label>
                                            <select class="form-select form-select-sm mb-3" name="status" id="admin-status-select" {{ $functions_disabled ? 'disabled' : '' }}>
                                                <option value="open" {{ $ticket->status == 'open' ? 'selected' : '' }}>Open</option>
                                                <option value="in_progress" {{ $ticket->status == 'in_progress' ? 'selected' : '' }}>On Progress</option>
                                                <option value="closed" {{ $ticket->status == 'closed' ? 'selected' : '' }}>Closed</option>
                                                <option value="rejected" {{ $ticket->status == 'rejected' ? 'selected' : '' }}>Rejected</option>
                                            </select>

                                            <!-- hidden fields filled by modal JS when closing -->
                                            <input type="hidden" name="closing_at" id="closing_at">
                                            <input type="hidden" name="tindak_lanjut_closing" id="tindak_lanjut_closing">
                                            <input type="hidden" name="media_closing" id="media_closing">

                                            <button type="submit" class="btn btn-sm btn-helpdesk-success" id="admin-status-submit" {{ $functions_disabled ? 'disabled' : '' }}><i class="fas fa-check me-1"></i> Update Status</button>
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
            @if(!empty($ticket))
                @include('admin.partials.tindak-lanjut-summary', ['ticket' => $ticket])
            @endif
            <div class="card followup-chat-card h-100 flex-fill" id="chat-card">
                <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">Chat Tindak Lanjut</h6>
                </div>
                <div class="card-body p-2 d-flex flex-column" style="height:100%; min-height:0;">
                    @if(!empty($ticket))
                        <div id="chat-messages-wrapper" style="flex:1 1 auto; min-height:0;">
                            <div id="chat-messages" class="h-100 overflow-auto chat-message-surface" style="display:flex;flex-direction:column;gap:10px;"></div>
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
                                    placeholder="Ketik pesan Anda..."
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
                        <div class="chat-empty-state"><div class="chat-empty-icon"><i class="far fa-comments"></i></div><strong>Belum ada tiket dipilih</strong><span>Pilih tiket dari Daftar Tiket untuk mulai percakapan</span></div>
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
        if (!msgs.length) {
            messagesEl.innerHTML = '<div class="chat-empty-state"><div class="chat-empty-icon"><i class="far fa-comments"></i></div><strong>Belum ada pesan</strong><span>Mulai percakapan untuk tindak lanjut tiket ini</span></div>';
            return;
        }
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
        btnSend.disabled = true;
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
        btnSend.disabled = false;
    });

    input && input.addEventListener('keydown', function(event) {
        if (event.key === 'Enter' && !event.shiftKey) {
            event.preventDefault();
            btnSend.click();
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
        const maxCompactHeight = 450;
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
    document.body.classList.add('helpdesk-followup');
    const sidebarToggle = document.getElementById('helpdesk-sidebar-toggle');
    const nativeToggle = document.getElementById('iconSidenav');
    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', function(){
            if (nativeToggle && window.innerWidth < 1200) nativeToggle.click();
            else document.body.classList.toggle('g-sidenav-pinned');
        });
    }
    const f = document.querySelector('footer');
    if (f) f.remove();
});
</script>

