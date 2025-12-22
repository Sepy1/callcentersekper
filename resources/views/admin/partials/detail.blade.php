<div class="row g-3">

    <div class="col-md-6">
        <label class="form-label text-xs fw-bold">Nomor Tiket</label>
        <div class="form-control bg-light">{{ $ticket->nomor_tiket }}</div>
    </div>

    <div class="col-md-6">
        <label class="form-label text-xs fw-bold">Status</label><br>
        <span class="badge bg-gradient-{{ $ticket->status == 'open' ? 'success' : ($ticket->status == 'in_progress' ? 'warning' : 'secondary') }}">
            {{ strtoupper(str_replace('_',' ', $ticket->status)) }}
        </span>
    </div>

    <div class="col-md-6">
        <label class="form-label text-xs fw-bold">Nama Pelapor</label>
        <div class="form-control bg-light">{{ $ticket->nama_pelapor }}</div>
    </div>

    @if(!empty($ticket->id_ktp))
    @php
        $cifs = \App\Models\Nasabah::where('no_ktp', $ticket->id_ktp)->pluck('cif')->toArray();
        $cifCount = count($cifs);
        $visible = array_slice($cifs, 0, 3);
        $hidden = array_slice($cifs, 3);
    @endphp
    <div class="col-md-6">
        <label class="form-label text-xs fw-bold">CIF (dari No. KTP)</label>
        <div class="form-control bg-light">
            @if($cifCount === 0)
                -
            @else
                <div class="cif-list">
                    @foreach($visible as $c)
                        <span class="badge bg-info text-dark me-1">{{ $c }}</span>
                    @endforeach
                    @if(count($hidden) > 0)
                        <span id="more-cifs" class="d-none">
                            @foreach($hidden as $c)
                                <span class="badge bg-info text-dark me-1">{{ $c }}</span>
                            @endforeach
                        </span>
                        <button type="button" id="btn-show-more-cifs" class="btn btn-sm btn-link">+{{ count($hidden) }} more</button>
                    @endif
                </div>
            @endif
        </div>
    </div>
    @endif

    <div class="col-md-6">
        <label class="form-label text-xs fw-bold">Email</label>
        <div class="form-control bg-light">{{ $ticket->email }}</div>
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

@push('dashboard')
<script>
document.addEventListener('DOMContentLoaded', function(){
    var btn = document.getElementById('btn-show-more-cifs');
    if(!btn) return;
    btn.addEventListener('click', function(){
        var more = document.getElementById('more-cifs');
        if(!more) return;
        more.classList.remove('d-none');
        btn.style.display = 'none';
    });
});
</script>
@endpush
