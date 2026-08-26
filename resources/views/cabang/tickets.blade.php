@extends('layouts.user_type.auth')

@section('content')
<style>
    .branch-ticket-table { width:100%; table-layout:fixed; font-size:.8rem; }
    .branch-ticket-table th,.branch-ticket-table td { padding:.35rem .45rem; vertical-align:middle; }
    .branch-ticket-table td { white-space:normal; word-break:break-word; }
    .branch-ticket-filters .form-control,.branch-ticket-filters .form-select { font-size:.78rem; min-height:31px; padding:.25rem .6rem; }
    .branch-ticket-filters .btn { font-size:.72rem; padding:.35rem .7rem; white-space:nowrap; }
</style>
<div class="container-fluid py-2">
    @if(session('success'))<div class="alert alert-success text-white">{{ session('success') }}</div>@endif
    @if($errors->any())<div class="alert alert-danger text-white">{{ $errors->first() }}</div>@endif

    <div class="card">
        <div class="card-header pb-0">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                <div><h6 class="mb-0">Daftar Tiket Cabang</h6><p class="text-xs text-secondary mb-0">Kode kantor: <strong>{{ $kodeKantor }}</strong></p></div>
                <button class="btn btn-sm bg-gradient-success rounded-pill px-3 mb-0" data-bs-toggle="modal" data-bs-target="#createTicketModal"><i class="fas fa-plus me-1"></i>Buat Tiket</button>
            </div>
            <form method="GET" class="row g-2 mt-3 branch-ticket-filters">
                <div class="col-md-5"><input class="form-control form-control-sm" name="q" value="{{ request('q') }}" placeholder="Cari nomor, pelapor, judul, atau kategori"></div>
                <div class="col-md-2"><select class="form-select form-select-sm rounded-pill" name="status"><option value="">Semua status</option><option value="open" @selected(request('status') === 'open')>Open</option><option value="in_progress" @selected(request('status') === 'in_progress')>In Progress</option><option value="closed" @selected(request('status') === 'closed')>Closed</option></select></div>
                <div class="col-md-3"><select class="form-select form-select-sm rounded-pill" name="kategori"><option value="">Semua kategori</option>@foreach($categories as $category)<option value="{{ $category->name }}" @selected(request('kategori') === $category->name)>{{ $category->name }}</option>@endforeach</select></div>
                <div class="col-md-2 d-flex gap-2"><button class="btn btn-sm bg-gradient-primary rounded-pill mb-0 flex-fill">Filter</button><a class="btn btn-sm btn-outline-secondary rounded-pill mb-0" href="{{ route('cabang.tickets') }}">Reset</a></div>
            </form>
        </div>
        <div class="card-body px-0 pb-2">
            <div class="table-responsive">
                <table class="table table-hover table-sm branch-ticket-table mb-0">
                    <thead><tr><th>Nomor Tiket</th><th>Pelapor</th><th>Kategori</th><th>Judul</th><th>Status</th><th>Dibuat</th></tr></thead>
                    <tbody>
                    @forelse($tickets as $ticket)
                        <tr>
                            <td class="px-4 text-sm fw-bold">{{ $ticket->nomor_tiket }}</td>
                            <td><span class="text-sm fw-bold">{{ $ticket->nama_pelapor }}</span><br><small>{{ $ticket->email }}</small></td>
                            <td class="text-sm">{{ $ticket->kategori }}</td>
                            <td class="text-sm">{{ $ticket->judul }}</td>
                            <td><span class="badge bg-gradient-{{ $ticket->status === 'open' ? 'success' : ($ticket->status === 'in_progress' ? 'warning' : 'secondary') }}">{{ str_replace('_', ' ', $ticket->status) }}</span></td>
                            <td class="text-sm text-secondary">{{ $ticket->created_at->format('d-m-Y H:i') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-secondary py-4">Belum ada tiket untuk kode kantor {{ $kodeKantor }}.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-4 pt-3">{{ $tickets->links('vendor.pagination.modern') }}</div>
        </div>
    </div>
</div>

<div class="modal fade" id="createTicketModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered"><div class="modal-content">
        <form method="POST" action="{{ route('cabang.tickets.store') }}" enctype="multipart/form-data">
            @csrf
            <div class="modal-header"><h5 class="modal-title">Buat Tiket Baru</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body"><div class="row g-3">
                <div class="col-md-6"><label class="form-label">Nama Pelapor</label><input class="form-control" name="nama_pelapor" value="{{ old('nama_pelapor') }}" required></div>
                <div class="col-md-6"><label class="form-label">Email</label><input type="email" class="form-control" name="email" value="{{ old('email') }}" required></div>
                <div class="col-md-6"><label class="form-label">Nomor HP</label><input class="form-control" name="hp" value="{{ old('hp') }}" required></div>
                <div class="col-md-6"><label class="form-label">Kategori</label><select class="form-select" name="kategori" required><option value="">Pilih kategori</option>@foreach($categories as $category)<option value="{{ $category->name }}" @selected(old('kategori') === $category->name)>{{ $category->name }}</option>@endforeach</select></div>
                <div class="col-md-6"><label class="form-label">Tipe Pelapor</label><select class="form-select" id="branch-reporter-type" name="tipe_pelapor" required><option value="Umum">Umum</option><option value="Nasabah" @selected(old('tipe_pelapor') === 'Nasabah')>Nasabah</option></select></div>
                <div class="col-md-6"><label class="form-label">Kode Kantor</label><input class="form-control" value="{{ $kodeKantor }}" disabled></div>
                <div class="col-12"><label class="form-label">Judul</label><input class="form-control" name="judul" value="{{ old('judul') }}" required></div>
                <div class="col-12"><label class="form-label">Detail</label><textarea class="form-control" name="detail" rows="4" required>{{ old('detail') }}</textarea></div>
                <div id="branch-customer-fields" class="col-12" style="display:none"><div class="row g-3">
                    <div class="col-md-6"><label class="form-label">ID KTP</label><input class="form-control" name="id_ktp" value="{{ old('id_ktp') }}"></div>
                    <div class="col-md-6"><label class="form-label">Nomor Rekening</label><input class="form-control" name="nomor_rekening" value="{{ old('nomor_rekening') }}"></div>
                    <div class="col-md-6"><label class="form-label">Nama Ibu</label><input class="form-control" name="nama_ibu" value="{{ old('nama_ibu') }}"></div>
                    <div class="col-md-6"><label class="form-label">Tempat Lahir</label><input class="form-control" name="tempat_lahir" value="{{ old('tempat_lahir') }}"></div>
                    <div class="col-md-6"><label class="form-label">Tanggal Lahir</label><input type="date" class="form-control" name="tgl_lahir" value="{{ old('tgl_lahir') }}"></div>
                    <div class="col-md-6"><label class="form-label">Alamat</label><input class="form-control" name="alamat" value="{{ old('alamat') }}"></div>
                    <div class="col-md-6"><label class="form-label">Upload KTP</label><input type="file" class="form-control" name="upload_ktp" accept=".jpg,.jpeg,.png,.pdf"></div>
                    <div class="col-md-6"><label class="form-label">Upload Bukti</label><input type="file" class="form-control" name="upload_bukti" accept=".jpg,.jpeg,.png,.pdf"></div>
                </div></div>
            </div></div>
            <div class="modal-footer"><button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button><button class="btn bg-gradient-success">Simpan Tiket</button></div>
        </form>
    </div></div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const type = document.getElementById('branch-reporter-type');
    const fields = document.getElementById('branch-customer-fields');
    const update = () => { fields.style.display = type.value === 'Nasabah' ? 'block' : 'none'; };
    type.addEventListener('change', update);
    update();
    @if($errors->any()) new bootstrap.Modal(document.getElementById('createTicketModal')).show(); @endif
});
</script>
@endpush
