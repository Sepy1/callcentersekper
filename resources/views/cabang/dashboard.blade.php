@extends('layouts.user_type.auth')

@section('content')
<style>
    .branch-dashboard-table { width:100%; table-layout:fixed; font-size:.8rem; }
    .branch-dashboard-table th,.branch-dashboard-table td { padding:.35rem .45rem; vertical-align:middle; }
    .branch-dashboard-table td { white-space:normal; word-break:break-word; }
</style>
<div class="container-fluid py-4">
    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div><h6 class="mb-0">Dashboard Cabang</h6><p class="text-xs text-secondary mb-0">Kode kantor: <strong>{{ $kodeKantor }}</strong></p></div>
                <a href="{{ route('cabang.tickets') }}" class="btn btn-sm bg-gradient-primary mb-0"><i class="fas fa-list me-1"></i>Daftar Tiket</a>
            </div>
            <div class="row">
                @foreach([
                    ['Total Tiket', $total, 'ni-collection', 'primary'],
                    ['Open', $open, 'ni-folder-17', 'success'],
                    ['Dalam Proses', $inProgress, 'ni-settings-gear-65', 'warning'],
                    ['Selesai', $closed, 'ni-check-bold', 'info'],
                ] as [$label, $value, $icon, $color])
                <div class="col-lg-3 col-md-6 col-sm-6 mb-3 mb-lg-0">
                    <div class="card h-100"><div class="card-body p-3"><div class="row">
                        <div class="col-8"><div class="numbers"><p class="text-sm mb-0 text-uppercase font-weight-bold">{{ $label }}</p><h5 class="font-weight-bolder mb-0">{{ $value }}</h5></div></div>
                        <div class="col-4 text-end"><div class="icon icon-shape bg-gradient-{{ $color }} shadow text-center border-radius-md"><i class="ni {{ $icon }} text-lg opacity-10 text-white"></i></div></div>
                    </div></div></div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="card mt-4">
        <div class="card-header pb-0"><h6>Tiket Terbaru</h6></div>
        <div class="card-body px-0 pt-2">
            <div class="table-responsive">
                <table class="table table-hover table-sm branch-dashboard-table mb-0">
                    <thead><tr><th>Nomor</th><th>Pelapor</th><th>Judul</th><th>Status</th><th>Dibuat</th></tr></thead>
                    <tbody>
                    @forelse($latestTickets as $ticket)
                        <tr>
                            <td class="px-4 text-sm fw-bold">{{ $ticket->nomor_tiket }}</td>
                            <td class="text-sm">{{ $ticket->nama_pelapor }}</td>
                            <td class="text-sm">{{ $ticket->judul }}</td>
                            <td><span class="badge bg-gradient-{{ $ticket->status === 'open' ? 'success' : ($ticket->status === 'in_progress' ? 'warning' : 'secondary') }}">{{ str_replace('_', ' ', $ticket->status) }}</span></td>
                            <td class="text-sm text-secondary">{{ $ticket->created_at->format('d-m-Y H:i') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-secondary py-4">Belum ada tiket untuk kantor ini.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
