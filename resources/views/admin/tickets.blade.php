@extends('layouts.user_type.auth')

@section('content')
<div class="container-fluid py-2">
    {{-- Toast Success (standard toast, centered top) --}}
    @if(session('success'))
    <div id="toast-top-center-wrap" class="position-fixed top-0 start-50 translate-middle-x p-3" style="z-index:1080;">
        <div id="toast-success" class="toast align-items-center border-0" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header bg-success text-white">
                <strong class="me-auto">Sukses</strong>
                <button type="button" class="btn-close btn-close-white ms-2 mb-1" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body">
                {{ session('success') }}
            </div>
        </div>
    </div>
    @endif
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header pb-0">
                    <div class="d-flex flex-wrap gap-3 justify-content-between align-items-center">
                        <h6 class="mb-0">Daftar Tiket</h6>
                    </div>
                    {{-- FILTER --}}
                    <form method="GET" class="d-flex gap-2 align-items-center mt-3">
                        {{-- Search --}}
                        <div class="input-group input-group-sm border">
                            <span class="input-group-text bg-white border-0">
                                <i class="fas fa-search text-secondary"></i>
                            </span>
                            <input
                                type="text"
                                name="q"
                                value="{{ request('q') }}"
                                class="form-control border-0"
                                placeholder="Cari tiket..."
                            >
                        </div>

                        {{-- Status --}}
                        <select name="status" class="form-select form-select-sm rounded-pill">
                            <option value="">Semua Status</option>
                            <option value="open" {{ request('status')=='open'?'selected':'' }}>Open</option>
                            <option value="in_progress" {{ request('status')=='in_progress'?'selected':'' }}>In Progress</option>
                            <option value="closed" {{ request('status')=='closed'?'selected':'' }}>Closed</option>
                        </select>

                        {{-- Kategori --}}
                        <select name="kategori" class="form-select form-select-sm rounded-pill">
                            <option value="">Semua Kategori</option>
                            <option value="Informasi" {{ request('kategori')=='Informasi'?'selected':'' }}>Informasi</option>
                            <option value="Layanan" {{ request('kategori')=='Layanan'?'selected':'' }}>Layanan</option>
                            <option value="Pengaduan" {{ request('kategori')=='Pengaduan'?'selected':'' }}>Pengaduan</option>
                        </select>

                        {{-- Submit --}}
                        <button class="btn btn-sm bg-gradient-primary rounded-pill px-3 mb-0">
                            Filter
                        </button>

                        {{-- Reset --}}
                        <a href="{{ route('admin.tickets') }}" class="btn btn-sm bg-gradient-primary rounded-pill px-3 mb-0">
                            Reset
                        </a>

                        {{-- Buat Tiket --}}
                        <button type="button" class="btn btn-success btn-sm rounded-pill px-3 mb-0" id="btn-buat-tiket">
                            <i class="fas fa-plus me-1"></i> Buat
                        </button>
                    </form>
                </div>
                <div class="card-body px-0 pt-0 pb-2">
                    <table class="table align-items-center mb-0">
                        <thead>
                            <tr>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Pelapor</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Kategori</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Judul</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Status</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Officer</th>
                                <th class="text-secondary opacity-7"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($tickets as $ticket)
                            <tr>
                                <td>
                                    <div class="d-flex px-2 py-1">
                                        <div>
                                            <img src="../assets/img/team-2.jpg" class="avatar avatar-sm me-3" alt="user">
                                        </div>
                                        <div class="d-flex flex-column justify-content-center">
                                            <h6 class="mb-0 text-sm">{{ $ticket->nama_pelapor }}</h6>
                                            <p class="text-xs text-secondary mb-0">{{ $ticket->email }}</p>
                                            <p class="text-xs text-secondary mb-0">{{ $ticket->nomor_tiket }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <p class="text-xs font-weight-bold mb-0">{{ $ticket->kategori }}</p>
                                    <p class="text-xs text-secondary mb-0">{{ $ticket->tipe_pelapor }}</p>
                                </td>
                                <td>
                                    <p class="text-xs font-weight-bold mb-0">{{ $ticket->judul }}</p>
                                    <p class="text-xs text-secondary mb-0">{{ $ticket->created_at }}</p>
                                </td>
                                <td class="align-middle text-center text-sm">
                                    @if($ticket->status == 'open')
                                        <span class="badge badge-sm bg-gradient-success">Open</span>
                                    @elseif($ticket->status == 'in_progress')
                                        <span class="badge badge-sm bg-gradient-warning">In Progress</span>
                                    @elseif($ticket->status == 'closed')
                                        <span class="badge badge-sm bg-gradient-secondary">Closed</span>
                                    @else
                                        <span class="badge badge-sm bg-gradient-secondary">{{ ucfirst($ticket->status) }}</span>
                                    @endif
                                </td>
                                <td class="align-middle text-center">
                                    <span class="text-secondary text-xs font-weight-bold">{{ $ticket->officer }}</span>
                                </td>
                                <td class="align-middle">
                                    <div class="d-flex justify-content-end">
                                        <div class="d-flex align-items-center gap-2" style="min-width:220px; background:transparent; box-shadow:none; border:0; padding:0;">
                                            <a href="{{ route('admin.tindak-lanjut', ['nomor_tiket' => $ticket->nomor_tiket]) }}"
                                               class="btn btn-sm bg-gradient-primary rounded-pill px-3 py-2 mb-0 text-white"
                                               style="display:inline-flex;align-items:center;justify-content:center;">
                                                Detail
                                            </a>
                                            <button type="button"
                                                    class="btn btn-sm bg-gradient-warning rounded-pill px-3 py-2 mb-0 text-white btn-edit-ticket"
                                                    style="display:inline-flex;align-items:center;justify-content:center;"
                                                    data-id="{{ $ticket->id }}"
                                                    data-nomor="{{ $ticket->nomor_tiket }}"
                                                    data-nama="{{ $ticket->nama_pelapor }}"
                                                    data-email="{{ $ticket->email }}"
                                                    data-kategori="{{ $ticket->kategori }}"
                                                    data-tipe_pelapor="{{ $ticket->tipe_pelapor }}"
                                                    data-officer="{{ $ticket->officer }}"
                                                    data-status="{{ $ticket->status }}"
                                                    data-judul="{{ $ticket->judul }}"
                                                    data-detail="{{ $ticket->detail }}"
                                                    data-id_ktp="{{ $ticket->id_ktp ?? '' }}"
                                                    data-nomor_rekening="{{ $ticket->nomor_rekening ?? '' }}"
                                                    data-hp="{{ $ticket->hp ?? '' }}"
                                                    data-nama_ibu="{{ $ticket->nama_ibu ?? '' }}"
                                                    data-alamat="{{ $ticket->alamat ?? '' }}"
                                                    data-tempat_lahir="{{ $ticket->tempat_lahir ?? '' }}"
                                                    data-tgl_lahir="{{ $ticket->tgl_lahir ?? '' }}"
                                                    data-kode_kantor="{{ $ticket->kode_kantor ?? '' }}"
                                            >Edit</button>
                                             <form method="POST" action="{{ route('admin.tickets.destroy', $ticket->id) }}" onsubmit="return confirm('Apakah anda yakin akan menghapus tiket?');" style="margin:0;">
                                                 @csrf
                                                 @method('DELETE')
                                                 <button type="submit"
                                                         class="btn btn-sm bg-gradient-danger rounded-pill px-3 py-2 mb-0 text-white"
                                                         style="display:inline-flex;align-items:center;justify-content:center;">
                                                     Hapus
                                                 </button>
                                             </form>
                                         </div>
                                     </div>
                                 </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <div class="d-flex justify-content-between align-items-center px-4 py-3 border-top">
                        <div class="text-xs text-secondary">
                            Menampilkan {{ $tickets->firstItem() }}–{{ $tickets->lastItem() }}
                            dari {{ $tickets->total() }} tiket
                        </div>
                        {{ $tickets->links('vendor.pagination.modern') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal Form Buat Tiket --}}
<div class="modal fade" id="modal-buat-tiket" tabindex="-1" aria-labelledby="modalBuatTiketLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form method="POST" action="{{ url('admin/tickets') }}" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="modalBuatTiketLabel">Buat Tiket Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Nama Pelapor</label>
                            <input type="text" name="nama_pelapor" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Email</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Kategori</label>
                            <select name="kategori" class="form-select" required>
                                <option value="">Pilih Kategori</option>
                                <option value="Informasi">Informasi</option>
                                <option value="Layanan">Layanan</option>
                                <option value="Pengaduan">Pengaduan</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Tipe Pelapor</label>
                            <select name="tipe_pelapor" id="tipe-pelapor" class="form-select" required>
                                <option value="">Pilih Tipe</option>
                                <option value="Umum">Umum</option>
                                <option value="Nasabah">Nasabah</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Officer</label>
                            <input type="text" name="officer" class="form-control">
                        </div>
                        {{-- Nasabah extra fields (hidden unless tipe_pelapor == Nasabah) --}}
                        <div id="nasabah-fields" class="w-100" style="display:none;">
                            <div class="row g-2 mt-2">
                                <div class="col-md-6">
                                    <label class="form-label small">ID KTP</label>
                                    <input type="text" name="id_ktp" id="id_ktp" class="form-control">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small">No. Rekening</label>
                                    <input type="text" name="nomor_rekening" id="nomor_rekening" class="form-control">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small">HP</label>
                                    <input type="text" name="hp" id="hp" class="form-control">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small">Nama Ibu</label>
                                    <input type="text" name="nama_ibu" id="nama_ibu" class="form-control">
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label small">Alamat</label>
                                    <input type="text" name="alamat" id="alamat" class="form-control">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small">Tempat Lahir</label>
                                    <input type="text" name="tempat_lahir" id="tempat_lahir" class="form-control">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small">Tgl Lahir</label>
                                    <input type="date" name="tgl_lahir" id="tgl_lahir" class="form-control">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small">Kode Kantor</label>
                                    <input type="text" name="kode_kantor" id="kode_kantor" class="form-control">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small">Upload KTP</label>
                                    <input type="file" name="upload_ktp" id="upload_ktp" class="form-control form-control-sm">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small">Upload Bukti</label>
                                    <input type="file" name="upload_bukti" id="upload_bukti" class="form-control form-control-sm">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Status</label>
                            <select name="status" class="form-select" required>
                                <option value="open">Open</option>
                                <option value="in_progress">In Progress</option>
                                <option value="closed">Closed</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold">Judul</label>
                            <input type="text" name="judul" class="form-control" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold">Detail Aduan</label>
                            <textarea name="detail" class="form-control" rows="3" required></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">Simpan Tiket</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Form Edit Tiket (reuse fields from Buat) --}}
<div class="modal fade" id="modal-edit-tiket" tabindex="-1" aria-labelledby="modalEditTiketLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form id="edit-form" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title" id="modalEditTiketLabel">Edit Tiket</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <!-- same structure as create modal -->
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Nama Pelapor</label>
                            <input type="text" name="nama_pelapor" id="edit-nama_pelapor" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Email</label>
                            <input type="email" name="email" id="edit-email" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Kategori</label>
                            <select name="kategori" id="edit-kategori" class="form-select" required>
                                <option value="">Pilih Kategori</option>
                                <option value="Informasi">Informasi</option>
                                <option value="Layanan">Layanan</option>
                                <option value="Pengaduan">Pengaduan</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Tipe Pelapor</label>
                            <select name="tipe_pelapor" id="edit-tipe_pelapor" class="form-select" required>
                                <option value="">Pilih Tipe</option>
                                <option value="Umum">Umum</option>
                                <option value="Nasabah">Nasabah</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Officer</label>
                            <input type="text" name="officer" id="edit-officer" class="form-control">
                        </div>

                        <div id="edit-nasabah-fields" class="w-100" style="display:none;">
                            <div class="row g-2 mt-2">
                                <div class="col-md-6">
                                    <label class="form-label small">ID KTP</label>
                                    <input type="text" name="id_ktp" id="edit-id_ktp" class="form-control">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small">No. Rekening</label>
                                    <input type="text" name="nomor_rekening" id="edit-nomor_rekening" class="form-control">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small">HP</label>
                                    <input type="text" name="hp" id="edit-hp" class="form-control">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small">Nama Ibu</label>
                                    <input type="text" name="nama_ibu" id="edit-nama_ibu" class="form-control">
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label small">Alamat</label>
                                    <input type="text" name="alamat" id="edit-alamat" class="form-control">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small">Tempat Lahir</label>
                                    <input type="text" name="tempat_lahir" id="edit-tempat_lahir" class="form-control">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small">Tgl Lahir</label>
                                    <input type="date" name="tgl_lahir" id="edit-tgl_lahir" class="form-control">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small">Kode Kantor</label>
                                    <input type="text" name="kode_kantor" id="edit-kode_kantor" class="form-control">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small">Upload KTP</label>
                                    <input type="file" name="upload_ktp" id="edit-upload_ktp" class="form-control form-control-sm">
                                    <div id="edit-upload_ktp_current" class="small text-muted mt-1"></div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small">Upload Bukti</label>
                                    <input type="file" name="upload_bukti" id="edit-upload_bukti" class="form-control form-control-sm">
                                    <div id="edit-upload_bukti_current" class="small text-muted mt-1"></div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">Status</label>
                            <select name="status" id="edit-status" class="form-select" required>
                                <option value="open">Open</option>
                                <option value="in_progress">In Progress</option>
                                <option value="closed">Closed</option>
                                <option value="rejected">Rejected</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold">Judul</label>
                            <input type="text" name="judul" id="edit-judul" class="form-control" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold">Detail Aduan</label>
                            <textarea name="detail" id="edit-detail" class="form-control" rows="3" required></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var btn = document.getElementById('btn-buat-tiket');
    var modal = new bootstrap.Modal(document.getElementById('modal-buat-tiket'));
    if(btn) {
        btn.addEventListener('click', function() {
            modal.show();
        });
    }

    // toggle nasabah fields
    const tipe = document.getElementById('tipe-pelapor');
    const nasabahFields = document.getElementById('nasabah-fields');
    const nasabahRequired = ['id_ktp','nomor_rekening','hp'];
    function setNasabahVisible(show) {
        nasabahFields.style.display = show ? 'block' : 'none';
        nasabahRequired.forEach(id => {
            const el = document.getElementById(id);
            if (el) el.required = !!show;
        });
    }
    if (tipe) {
        tipe.addEventListener('change', function(){
            setNasabahVisible(this.value === 'Nasabah');
        });
        // initial state
        setNasabahVisible(tipe.value === 'Nasabah');
    }

    // If a success flash exists, show standard toast at center-top
    var hasSuccess = {!! json_encode((bool)session('success')) !!};
    if (hasSuccess) {
        var toastEl = document.getElementById('toast-success');
        if (toastEl) {
            var t = new bootstrap.Toast(toastEl, { delay: 3000 });
            t.show();
        }
    }

    // helper to show edit modal and fill fields
    const editModalEl = document.getElementById('modal-edit-tiket');
    const editModal = editModalEl ? new bootstrap.Modal(editModalEl) : null;
    const editForm = document.getElementById('edit-form');

    document.querySelectorAll('.btn-edit-ticket').forEach(function(b){
        b.addEventListener('click', function(){
            const id = this.dataset.id;
            // set action URL
            editForm.action = '/admin/tickets/' + id;
            // fill fields
            document.getElementById('edit-nama_pelapor').value = this.dataset.nama || '';
            document.getElementById('edit-email').value = this.dataset.email || '';
            document.getElementById('edit-kategori').value = this.dataset.kategori || '';
            document.getElementById('edit-tipe_pelapor').value = this.dataset.tipe_pelapor || '';
            document.getElementById('edit-officer').value = this.dataset.officer || '';
            document.getElementById('edit-status').value = this.dataset.status || 'open';
            document.getElementById('edit-judul').value = this.dataset.judul || '';
            document.getElementById('edit-detail').value = this.dataset.detail || '';
            // nasabah fields
            document.getElementById('edit-id_ktp').value = this.dataset.id_ktp || '';
            document.getElementById('edit-nomor_rekening').value = this.dataset.nomor_rekening || '';
            document.getElementById('edit-hp').value = this.dataset.hp || '';
            document.getElementById('edit-nama_ibu').value = this.dataset.nama_ibu || '';
            document.getElementById('edit-alamat').value = this.dataset.alamat || '';
            document.getElementById('edit-tempat_lahir').value = this.dataset.tempat_lahir || '';
            document.getElementById('edit-tgl_lahir').value = this.dataset.tgl_lahir || '';
            document.getElementById('edit-kode_kantor').value = this.dataset.kode_kantor || '';
            // show/hide nasabah block
            const showNasabah = (this.dataset.tipe_pelapor === 'Nasabah');
            document.getElementById('edit-nasabah-fields').style.display = showNasabah ? 'block' : 'none';
            // clear file inputs and current file display
            document.getElementById('edit-upload_ktp').value = '';
            document.getElementById('edit-upload_bukti').value = '';
            document.getElementById('edit-upload_ktp_current').innerText = ''; // server side current file not provided in list
            document.getElementById('edit-upload_bukti_current').innerText = '';
            // show modal
            if (editModal) editModal.show();
        });
    });

    // sync nasabah visibility on tipe change inside edit modal
    const tipeEdit = document.getElementById('edit-tipe_pelapor');
    if (tipeEdit) {
        tipeEdit.addEventListener('change', function(){ document.getElementById('edit-nasabah-fields').style.display = this.value === 'Nasabah' ? 'block' : 'none'; });
    }
});

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

document.addEventListener('DOMContentLoaded', function(){
    const f = document.querySelector('footer');
    if (f) f.remove();
});

(function(){
    function fitContainer() {
        const container = document.querySelector('.container-fluid');
        if (!container) return;
        container.style.transformOrigin = 'top center';
        container.style.transition = 'transform 160ms ease';
        // reset to natural size for measurement
        container.style.transform = 'none';
        // small delay to ensure layout settled
        requestAnimationFrame(()=> {
            const cw = container.scrollWidth;
            const ch = container.scrollHeight;
            const vw = window.innerWidth;
            const vh = window.innerHeight;
            const scaleW = vw / cw;
            const scaleH = vh / ch;
            const scale = Math.min(1, scaleW, scaleH);
            container.style.transform = 'scale(' + scale + ')';
            // prevent page scrollbar showing
            document.documentElement.style.overflow = 'hidden';
            document.body.style.overflow = 'hidden';
        });
    }
    window.addEventListener('resize', fitContainer);
    window.addEventListener('orientationchange', fitContainer);
    document.addEventListener('DOMContentLoaded', fitContainer);
    // also run after a short delay to catch images/fonts load
    setTimeout(fitContainer, 300);
})();
</script>
@endsection
