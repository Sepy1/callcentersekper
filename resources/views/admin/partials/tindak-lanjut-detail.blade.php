{{-- Officer diambil dari tabel pivot --}}
@php
    $officerList = [];
    if (isset($ticket)) {
        $officerList = $ticket->officers()->get();
    }
@endphp
<div class="row">
    <div class="col-12">
        <div class="card bg-transparent shadow-xl w-100">
            <div class="overflow-hidden position-relative border-radius-xl" style="background-image: url('../assets/img/curved-images/curved14.jpg');">
                <span class="mask bg-gradient-dark"></span>
                <div class="card-body position-relative z-index-1 p-3">
                    <i class="fas fa-ticket-alt text-white p-2"></i>
                    <h5 class="text-white mt-4 mb-3 pb-2">{{ $ticket->nomor_tiket }}</h5>
                    <div class="d-flex flex-wrap mb-3">
                        <div class="me-4">
                            <p class="text-white text-sm opacity-8 mb-0">Nama Pelapor</p>
                            <h6 class="text-white mb-0">{{ $ticket->nama_pelapor }}</h6>
                        </div>
                        <div class="me-4">
                            <p class="text-white text-sm opacity-8 mb-0">Email</p>
                            <h6 class="text-white mb-0">{{ $ticket->email }}</h6>
                        </div>
                        <div class="me-4">
                            <p class="text-white text-sm opacity-8 mb-0">Kategori</p>
                            <h6 class="text-white mb-0">{{ $ticket->kategori }}</h6>
                        </div>
                        <div>
                            <p class="text-white text-sm opacity-8 mb-0">Status</p>
                            <h6 class="mb-0">
                                @if($ticket->status == 'open')
                                    <span class="badge bg-gradient-success px-3 py-2" style="font-size:0.9em;border-radius:8px;">Open</span>
                                @elseif($ticket->status == 'in_progress')
                                    <span class="badge bg-gradient-warning px-3 py-2" style="font-size:0.9em;border-radius:8px;">In Progress</span>
                                @elseif($ticket->status == 'closed')
                                    <span class="badge bg-gradient-secondary px-3 py-2" style="font-size:0.9em;border-radius:8px;">Closed</span>
                                @elseif($ticket->status == 'rejected')
                                    <span class="badge bg-gradient-danger px-3 py-2" style="font-size:0.9em;border-radius:8px;">Rejected</span>
                                @else
                                    <span class="badge bg-gradient-secondary px-3 py-2" style="font-size:0.9em;border-radius:8px;">{{ ucfirst($ticket->status) }}</span>
                                @endif
                            </h6>
                        </div>
                    </div>
                    <hr class="horizontal light my-3">
                    <div class="d-flex flex-column flex-wrap">
                        <div class="mb-2">
                            <p class="text-white text-sm opacity-8 mb-0">Officer</p>
                            <h6 class="text-white mb-0">
                                @if(count($officerList))
                                    @foreach($officerList as $officer)
                                        @php
                                            $pivot = $officer->pivot ?? null;
                                        @endphp
                                        @if($pivot && $pivot->status == 'proses_qa')
                                            <span class="badge bg-gradient-success text-white me-1 mb-1" style="font-size:1em;">{{ $officer->name }}</span>
                                        @else
                                            <span class="text-white me-1 mb-1">{{ $officer->name }}</span>
                                        @endif
                                    @endforeach
                                @else
                                    -
                                @endif
                            </h6>
                        </div>
                        <div class="mb-2">
                            <p class="text-white text-sm opacity-8 mb-0">Judul</p>
                            <h6 class="text-white mb-0">{{ $ticket->judul }}</h6>
                        </div>
                        <div class="mb-2">
                            <p class="text-white text-sm opacity-8 mb-0">Detail Aduan</p>
                            <h6 class="text-white mb-0">{{ $ticket->detail }}</h6>
                        </div>
                        <div>
                            <p class="text-white text-sm opacity-8 mb-0">Dibuat</p>
                            <h6 class="text-white mb-0">{{ $ticket->created_at }}</h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
