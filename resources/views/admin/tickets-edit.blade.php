@extends('layouts.user_type.auth')

@section('content')
<div class="container-fluid py-4">
	<div class="row">
		<div class="col-md-8">
			<div class="card">
				<div class="card-header"><h6 class="mb-0">Edit Tiket: {{ $ticket->nomor_tiket }}</h6></div>
				<div class="card-body">
					<form method="POST" action="{{ route('admin.tickets.update', $ticket->id) }}">
						@csrf
						@method('PUT')

						<div class="mb-2">
							<label class="form-label">Nama Pelapor</label>
							<input type="text" name="nama_pelapor" class="form-control" value="{{ old('nama_pelapor', $ticket->nama_pelapor) }}" required>
						</div>
						<div class="mb-2">
							<label class="form-label">Email</label>
							<input type="email" name="email" class="form-control" value="{{ old('email', $ticket->email) }}" required>
						</div>
						<div class="mb-2">
							<label class="form-label">Kategori</label>
							<input type="text" name="kategori" class="form-control" value="{{ old('kategori', $ticket->kategori) }}" required>
						</div>
						<div class="mb-2">
							<label class="form-label">Officer</label>
							<input type="text" name="officer" class="form-control" value="{{ old('officer', $ticket->officer) }}">
						</div>
						<div class="mb-2">
							<label class="form-label">Status</label>
							<select name="status" class="form-select" required>
								<option value="open" {{ $ticket->status=='open'?'selected':'' }}>Open</option>
								<option value="in_progress" {{ $ticket->status=='in_progress'?'selected':'' }}>In Progress</option>
								<option value="closed" {{ $ticket->status=='closed'?'selected':'' }}>Closed</option>
								<option value="rejected" {{ $ticket->status=='rejected'?'selected':'' }}>Rejected</option>
							</select>
						</div>
						<div class="mb-2">
							<label class="form-label">Judul</label>
							<input type="text" name="judul" class="form-control" value="{{ old('judul', $ticket->judul) }}" required>
						</div>
						<div class="mb-2">
							<label class="form-label">Detail</label>
							<textarea name="detail" class="form-control" rows="4" required>{{ old('detail', $ticket->detail) }}</textarea>
						</div>

						<div class="d-flex justify-content-end">
							<a href="{{ route('admin.tickets') }}" class="btn btn-secondary me-2">Batal</a>
							<button type="submit" class="btn btn-primary">Simpan Perubahan</button>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection
