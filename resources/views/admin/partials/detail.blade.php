<div class="row g-3">

    <div class="col-md-6">
        <label class="form-label text-xs fw-bold">Nomor Tiket</label>
        <div class="form-control bg-light">{{ $ticket->nomor_tiket }}</div>
    </div>

    <div class="col-md-6">
        <label class="form-label text-xs fw-bold">Status</label><br>
        @if($ticket->status == 'open')
            <span class="badge bg-gradient-success">Open</span>
        @elseif($ticket->status == 'in_progress')
            <span class="badge bg-gradient-warning">In Progress</span>
        @elseif($ticket->status == 'closed')
            <span class="badge bg-gradient-secondary">Closed</span>
        @endif
    </div>

    <div class="col-md-6">
        <label class="form-label text-xs fw-bold">Nama Pelapor</label>
        <div class="form-control bg-light">{{ $ticket->nama_pelapor }}</div>
    </div>

    <div class="col-md-6">
        <label class="form-label text-xs fw-bold">Email</label>
        <div class="form-control bg-light">{{ $ticket->email }}</div>
    </div>

    <div class="col-md-6">
        <label class="form-label text-xs fw-bold">Kategori</label>
        <div class="form-control bg-light">{{ $ticket->kategori }}</div>
    </div>

    <div class="col-md-6">
        <label class="form-label text-xs fw-bold">Officer</label>
        <div class="form-control bg-light">{{ $ticket->officer ?? '-' }}</div>
    </div>

    <div class="col-md-6">
        <label class="form-label text-xs fw-bold">Tanggal Dibuat</label>
        <div class="form-control bg-light">
            {{ \Carbon\Carbon::parse($ticket->created_at)->format('d M Y H:i') }}
        </div>
    </div>

    <div class="col-12">
        <label class="form-label text-xs fw-bold">Judul</label>
        <div class="form-control bg-light">{{ $ticket->judul }}</div>
    </div>

    <div class="col-12">
        <label class="form-label text-xs fw-bold">Deskripsi</label>
        <div class="form-control bg-light" style="min-height:120px">
            {{ $ticket->deskripsi }}
        </div>
    </div>

</div>
