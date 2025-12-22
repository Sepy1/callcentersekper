<dl class="row mb-0">
    <dt class="col-sm-4">Nomor Tiket</dt>
    <dd class="col-sm-8">{{ $ticket->nomor_tiket }}</dd>
    <dt class="col-sm-4">Nama Pelapor</dt>
    <dd class="col-sm-8">{{ $ticket->nama_pelapor }}</dd>
    <dt class="col-sm-4">Email</dt>
    <dd class="col-sm-8">{{ $ticket->email }}</dd>
    <dt class="col-sm-4">No. HP</dt>
    <dd class="col-sm-8">{{ $ticket->hp }}</dd>
    <dt class="col-sm-4">Kategori</dt>
    <dd class="col-sm-8">{{ $ticket->kategori }}</dd>
    <dt class="col-sm-4">Judul</dt>
    <dd class="col-sm-8">{{ $ticket->judul }}</dd>
    <dt class="col-sm-4">Detail</dt>
    <dd class="col-sm-8">{{ $ticket->detail }}</dd>
    <dt class="col-sm-4">Tindak Lanjut</dt>
    <dd class="col-sm-8">{{ $ticket->tindak_lanjut }}</dd>
    <dt class="col-sm-4">QA Summary</dt>
    <dd class="col-sm-8">{{ $ticket->qa_summary }}</dd>
    <dt class="col-sm-4">Status</dt>
    <dd class="col-sm-8">
        @if($ticket->status == 'open')
            <span class="badge bg-gradient-success">Open</span>
        @elseif($ticket->status == 'in_progress')
            <span class="badge bg-gradient-warning">In Progress</span>
        @elseif($ticket->status == 'closed')
            <span class="badge bg-gradient-secondary">Closed</span>
        @else
            <span class="badge bg-gradient-secondary">{{ ucfirst($ticket->status) }}</span>
        @endif
    </dd>
    <dt class="col-sm-4">Officer</dt>
    <dd class="col-sm-8">{{ $ticket->officer }}</dd>
    <dt class="col-sm-4">Waktu Eskalasi</dt>
    <dd class="col-sm-8">{{ $ticket->waktu_eskalasi }}</dd>
    <dt class="col-sm-4">Tipe Pelapor</dt>
    <dd class="col-sm-8">{{ $ticket->tipe_pelapor }}</dd>
    <dt class="col-sm-4">Nasabah?</dt>
    <dd class="col-sm-8">{{ $ticket->is_nasabah ? 'Ya' : 'Tidak' }}</dd>
    <dt class="col-sm-4">ID KTP</dt>
    <dd class="col-sm-8">{{ $ticket->id_ktp }}</dd>
    <dt class="col-sm-4">CIF</dt>
    <dd class="col-sm-8">
        @php $cifs = $ticket->cifs ?? []; @endphp
        @if(!empty($cifs))
            @php $first = array_slice($cifs, 0, 3); $remaining = count($cifs) - count($first); @endphp
            {{ implode(', ', $first) }}
                @if($remaining > 0)
                <a href="#" class="ms-2 small show-more-cif" data-ticket-id="{{ $ticket->id }}" data-remaining="{{ $remaining }}">+{{ $remaining }} more</a>
                <div class="all-cifs d-none mt-1 small text-muted">{{ implode(', ', $cifs) }}</div>
            @endif
        @else
            -
        @endif
    </dd>
    <dt class="col-sm-4">Nomor Rekening</dt>
    <dd class="col-sm-8">{{ $ticket->nomor_rekening }}</dd>
    <dt class="col-sm-4">Nama Ibu</dt>
    <dd class="col-sm-8">{{ $ticket->nama_ibu }}</dd>
    <dt class="col-sm-4">Alamat</dt>
    <dd class="col-sm-8">{{ $ticket->alamat }}</dd>
    <dt class="col-sm-4">Tempat, Tgl Lahir</dt>
    <dd class="col-sm-8">{{ $ticket->tempat_lahir }}, {{ $ticket->tgl_lahir }}</dd>
    <dt class="col-sm-4">Kode Kantor</dt>
    <dd class="col-sm-8">{{ $ticket->kode_kantor }}</dd>
    <dt class="col-sm-4">Upload KTP</dt>
    <dd class="col-sm-8">
        @if($ticket->upload_ktp)
            <a href="{{ asset('storage/' . $ticket->upload_ktp) }}" target="_blank">Lihat File</a>
        @else
            -
        @endif
    </dd>
    <dt class="col-sm-4">Upload Bukti</dt>
    <dd class="col-sm-8">
        @if($ticket->upload_bukti)
            <a href="{{ asset('storage/' . $ticket->upload_bukti) }}" target="_blank">Lihat File</a>
        @else
            -
        @endif
    </dd>
    <dt class="col-sm-4">Media Closing</dt>
    <dd class="col-sm-8">{{ $ticket->media_closing }}</dd>
    <dt class="col-sm-4">Dibuat</dt>
    <dd class="col-sm-8">{{ $ticket->created_at }}</dd>
    <dt class="col-sm-4">Diupdate</dt>
    <dd class="col-sm-8">{{ $ticket->updated_at }}</dd>
</dl>
