@extends('layouts.user_type.auth')

@section('content')
<div class="container-fluid py-4">
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
                        <a href="{{ url('officer/tickets') }}" class="btn btn-sm bg-gradient-primary rounded-pill px-3 mb-0">
                            Reset
                        </a>
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
                                    <a
                                        href="{{ url('officer/tindak-lanjut') }}?nomor_tiket={{ urlencode($ticket->nomor_tiket) }}"
                                        class="btn btn-sm btn-info text-white rounded-pill px-3"
                                        style="z-index:1056; position:relative;"
                                    >
                                        Detail
                                    </a>
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
@endsection
