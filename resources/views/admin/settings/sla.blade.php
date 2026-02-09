@extends('layouts.user_type.auth')

@section('content')
<div class="container-fluid py-4">
  <div class="row">
    <div class="col-lg-6 mb-lg-0 mb-4 d-flex">
      <div class="card h-100 w-100">
        <div class="card-body p-3 d-flex flex-column" style="height:100%;">
          <h6 class="text-uppercase font-weight-bold mb-3">Pengaturan SLA (Hari)</h6>
          <h8>Sistem akan mengirim notifikasi aduan yang belum selesai pada H-1</h8>

          <div style="flex:1 1 auto;">
            @if(session('success'))
              <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <form method="POST" action="{{ url('/admin/settings/sla') }}">
              @csrf
              <div class="mb-3">
                <label for="sla_days" class="form-label"></label>
                <input type="number" class="form-control" id="sla_days" name="sla_days" value="{{ old('sla_days', $sla_days ?? 2) }}" min="1" max="365" required>
                @error('sla_days')<div class="text-danger mt-1">{{ $message }}</div>@enderror
              </div>
              <div class="d-flex justify-content-end">
                <button class="btn btn-primary">Simpan</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>

    <div class="col-lg-6 mb-lg-0 mb-4 d-flex">
      <div class="card h-100 w-100">
        <div class="card-body p-3 d-flex flex-column" style="height:100%;">
          <h6 class="text-uppercase font-weight-bold mb-3">Manajemen Kategori Tiket</h6>

          <div style="flex:1 1 auto; overflow:hidden;">
            @if(session('category_success'))
              <div class="alert alert-success">{{ session('category_success') }}</div>
            @endif

            <form method="POST" action="{{ route('admin.settings.categories.store') }}" class="mb-3">
              @csrf
              <div class="input-group input-group-sm">
                <input name="name" class="form-control" placeholder="Nama kategori" required>
                <input name="description" class="form-control" placeholder="Deskripsi (opsional)">
                <button class="btn btn-outline-primary" type="submit">Tambah</button>
              </div>
            </form>
            <div style="max-height:320px; overflow-y:auto;">
              <ul class="list-group">
                @foreach($categories ?? [] as $cat)
                  <li class="list-group-item">
                    <div class="d-flex align-items-center">
                      <form method="POST" action="{{ route('admin.settings.categories.update', $cat->id) }}" class="d-flex flex-grow-1 align-items-center me-2">
                        @csrf
                        @method('PUT')
                        <input type="text" name="name" value="{{ $cat->name }}" class="form-control form-control-sm me-2" style="width:40%; min-width:140px;">
                        <input type="text" name="description" value="{{ $cat->description }}" class="form-control form-control-sm me-2" style="width:40%; min-width:160px;">
                        <button class="btn btn-success btn-sm" type="submit">OK</button>
                      </form>
                      <form method="POST" action="{{ route('admin.settings.categories.destroy', $cat->id) }}" onsubmit="return confirm('Hapus kategori?')">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-danger btn-sm ms-1" type="submit">Hapus</button>
                      </form>
                    </div>
                  </li>
                @endforeach
              </ul>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

@endsection

<script>
  (function(){
    // auto-hide alerts (SLA and category) after 2 seconds
    setTimeout(function(){
      try {
        document.querySelectorAll('.alert').forEach(function(el){
          el.style.transition = 'opacity 200ms ease-out, height 200ms ease-out';
          el.style.opacity = '0';
          setTimeout(function(){ el.remove(); }, 250);
        });
      } catch(e) { /* ignore in non-browser context */ }
    }, 2000);
  })();
</script>
