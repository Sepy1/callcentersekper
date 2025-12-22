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
            <div
                class="overflow-hidden position-relative border-radius-xl"
                style="background-image: url('../assets/img/curved-images/curved14.jpg');"
            >
                <span class="mask bg-gradient-dark"></span>

                <div class="card-body position-relative z-index-1 p-3">
                    <i class="fas fa-ticket-alt text-white p-2"></i>

                    {{-- NOMOR TIKET --}}
                    <h5 class="text-white mt-4 mb-3 pb-2">
                        {{ $ticket->nomor_tiket }}
                    </h5>

                    {{-- INFO UTAMA --}}
                    <div class="d-flex flex-wrap gap-4 mb-3">

                        {{-- Nama --}}
                        <div>
                            <p class="text-white text-sm opacity-8 mb-0">Nama Pelapor</p>
                            <h6 class="text-white mb-0">{{ $ticket->nama_pelapor }}</h6>
                        </div>

                        {{-- Email --}}
                        <div>
                            <p class="text-white text-sm opacity-8 mb-0">Email</p>
                            <h6 class="text-white mb-0">{{ $ticket->email }}</h6>
                        </div>

                        {{-- CIF (nasabah_id) --}}
                        @php $cifs = $ticket->cifs ?? []; @endphp
                        @if(!empty($cifs))
                        <div>
                            <p class="text-white text-sm opacity-8 mb-0">CIF</p>
                            @php $first = array_slice($cifs,0,3); $remaining = count($cifs) - count($first); @endphp
                            <h6 class="text-white mb-0">
                                {{ implode(', ', $first) }}
                                    @if($remaining > 0)
                                    <a href="#" class="ms-2 small show-more-cif text-white" data-ticket-id="{{ $ticket->id }}" data-remaining="{{ $remaining }}">+{{ $remaining }} more</a>
                                @endif
                            </h6>
                            @if($remaining > 0)
                                <div class="all-cifs d-none mt-1 small text-white-50">{{ implode(', ', $cifs) }}</div>
                            @endif
                        </div>
                        @endif

                        {{-- HP + ICON WHATSAPP (SVG) --}}
                        <div>
                            <p class="text-white text-sm opacity-8 mb-0">No. HP</p>

                            <div class="d-flex align-items-center gap-2">
                                <h6 class="text-white mb-0">
                                    {{ $ticket->hp ?? '-' }}
                                </h6>

                                @if(!empty($ticket->hp))
                                    <a
                                        href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $ticket->hp) }}"
                                        target="_blank"
                                        title="Chat Pelapor via WhatsApp"
                                        style="display:inline-flex;align-items:center;"
                                    >
                                        {{-- SVG WhatsApp --}}
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            width="16"
                                            height="16"
                                            viewBox="0 0 32 32"
                                            fill="#25D366"
                                            aria-label="WhatsApp"
                                        >
                                            <path d="M16 0C7.163 0 0 7.163 0 16c0 2.82.74 5.59 2.14 8.02L0 32l8.19-2.14A15.9 15.9 0 0 0 16 32c8.837 0 16-7.163 16-16S24.837 0 16 0z"/>
                                            <path fill="#fff" d="M24.27 19.6c-.36-.18-2.12-1.05-2.45-1.17-.33-.12-.57-.18-.81.18-.24.36-.93 1.17-1.14 1.41-.21.24-.42.27-.78.09-.36-.18-1.52-.56-2.9-1.78-1.07-.95-1.79-2.13-2-2.49-.21-.36-.02-.55.16-.73.16-.16.36-.42.54-.63.18-.21.24-.36.36-.6.12-.24.06-.45-.03-.63-.09-.18-.81-1.95-1.11-2.67-.3-.72-.6-.62-.81-.63h-.69c-.24 0-.63.09-.96.45-.33.36-1.26 1.23-1.26 3 0 1.77 1.29 3.48 1.47 3.72.18.24 2.54 3.87 6.16 5.43.86.37 1.53.59 2.05.75.86.27 1.65.23 2.27.14.69-.1 2.12-.87 2.42-1.71.3-.84.3-1.56.21-1.71-.09-.15-.33-.24-.69-.42z"/>
                                        </svg>
                                    </a>
                                @endif
                            </div>
                        </div>

                        {{-- Kategori --}}
                        <div>
                            <p class="text-white text-sm opacity-8 mb-0">Kategori</p>
                            <h6 class="text-white mb-0">{{ $ticket->kategori }}</h6>
                        </div>

                        {{-- Status --}}
                        <div>
                            <p class="text-white text-sm opacity-8 mb-0">Status</p>
                            <h6 class="mb-0">
                                @if($ticket->status == 'open')
                                    <span class="badge bg-gradient-success px-3 py-2">Open</span>
                                @elseif($ticket->status == 'in_progress')
                                    <span class="badge bg-gradient-warning px-3 py-2">In Progress</span>
                                @elseif($ticket->status == 'closed')
                                    <span class="badge bg-gradient-secondary px-3 py-2">Closed</span>
                                @elseif($ticket->status == 'rejected')
                                    <span class="badge bg-gradient-danger px-3 py-2">Rejected</span>
                                @else
                                    <span class="badge bg-gradient-secondary px-3 py-2">
                                        {{ ucfirst($ticket->status) }}
                                    </span>
                                @endif
                            </h6>
                        </div>

                    </div>

                    <hr class="horizontal light my-3">

                    {{-- DETAIL --}}
                    <div class="d-flex flex-column flex-wrap">

                        {{-- Officer --}}
                        <div class="mb-2">
                            <p class="text-white text-sm opacity-8 mb-0">Officer</p>
                            <h6 class="text-white mb-0">
                                @if(count($officerList))
                                    @foreach($officerList as $officer)
                                        @php $pivot = $officer->pivot ?? null; @endphp
                                        @if($pivot && $pivot->status == 'proses_qa')
                                            <span class="badge bg-gradient-success me-1 mb-1">
                                                {{ $officer->name }}
                                            </span>
                                        @else
                                            <span class="text-white me-1 mb-1">
                                                {{ $officer->name }}
                                            </span>
                                        @endif
                                    @endforeach
                                @else
                                    -
                                @endif
                            </h6>
                        </div>

                        {{-- Judul --}}
                        <div class="mb-2">
                            <p class="text-white text-sm opacity-8 mb-0">Judul</p>
                            <h6 class="text-white mb-0">{{ $ticket->judul }}</h6>
                        </div>

                        {{-- Detail Aduan --}}
                        <div class="mb-2">
                            <p class="text-white text-sm opacity-8 mb-0">Detail Aduan</p>
                            <h6 class="text-white mb-0">{{ $ticket->detail }}</h6>
                        </div>

                        {{-- Dibuat --}}
                        <div>
                            <p class="text-white text-sm opacity-8 mb-0">Dibuat</p>
                            <h6 class="text-white mb-0">
                                {{ $ticket->created_at }}
                            </h6>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
