@extends('layouts.user_type.auth')

@section('content')
<style>
    .user-management-table{width:100%;table-layout:fixed;font-size:.8rem}.user-management-table th,.user-management-table td{padding:.4rem .5rem;vertical-align:middle}.user-management-table td{white-space:normal;word-break:break-word}.user-management-filters .form-control,.user-management-filters .form-select{font-size:.78rem;min-height:31px;padding:.25rem .6rem}.user-management-filters .btn,.user-management-table .btn{font-size:.72rem;padding:.3rem .65rem;white-space:nowrap}
</style>
<div class="container-fluid py-2">
    @if(session('success'))<div class="alert alert-success text-white py-2 text-sm">{{ session('success') }}</div>@endif
    @if($errors->any())<div class="alert alert-danger text-white py-2 text-sm">{{ $errors->first() }}</div>@endif

    <div class="card mb-4">
        <div class="card-header pb-0">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                <h6 class="mb-0">Manajemen User</h6>
                <button class="btn btn-sm bg-gradient-success rounded-pill px-3 mb-0" data-bs-toggle="modal" data-bs-target="#createUserModal"><i class="fas fa-user-plus me-1"></i>Tambah User</button>
            </div>
            <form method="GET" class="row g-2 mt-3 user-management-filters">
                <div class="col-md-7"><input class="form-control form-control-sm" name="q" value="{{ request('q') }}" placeholder="Cari nama, email, no. HP, atau kode kantor"></div>
                <div class="col-md-3"><select class="form-select form-select-sm rounded-pill" name="role"><option value="">Semua role</option>@foreach(['admin','qa','officer','cabang'] as $role)<option value="{{ $role }}" @selected(request('role') === $role)>{{ ucfirst($role) }}</option>@endforeach</select></div>
                <div class="col-md-2 d-flex gap-2"><button class="btn btn-sm bg-gradient-primary rounded-pill mb-0 flex-fill">Filter</button><a href="{{ route('admin.users') }}" class="btn btn-sm btn-outline-secondary rounded-pill mb-0">Reset</a></div>
            </form>
        </div>
        <div class="card-body px-0 pb-2">
            <div class="table-responsive">
                <table class="table table-hover table-sm user-management-table mb-0">
                    <thead><tr><th style="width:22%">User</th><th style="width:19%">Email</th><th style="width:13%">No. HP</th><th style="width:11%">Role</th><th style="width:13%">Kode Kantor</th><th style="width:22%" class="text-end">Aksi</th></tr></thead>
                    <tbody>
                    @forelse($users as $user)
                        <tr>
                            <td class="px-4"><span class="fw-bold">{{ $user->name }}</span>@if($user->is(auth()->user()))<span class="badge bg-gradient-info ms-1">Anda</span>@endif</td>
                            <td>{{ $user->email }}</td><td>{{ $user->no_hp ?: '-' }}</td>
                            <td><span class="badge bg-gradient-{{ $user->role === 'admin' ? 'danger' : ($user->role === 'qa' ? 'info' : ($user->role === 'officer' ? 'warning' : 'success')) }}">{{ ucfirst($user->role) }}</span></td>
                            <td>{{ $user->kode_kantor ?: '-' }}</td>
                            <td class="text-end pe-4">
                                <button type="button" class="btn btn-sm bg-gradient-warning text-white rounded-pill mb-0 edit-user" data-bs-toggle="modal" data-bs-target="#editUserModal" data-user-id="{{ $user->id }}" data-action="{{ route('admin.users.update', $user) }}" data-name="{{ $user->name }}" data-email="{{ $user->email }}" data-no-hp="{{ $user->no_hp }}" data-role="{{ $user->role }}" data-kode-kantor="{{ $user->kode_kantor }}"><i class="fas fa-user-edit me-1"></i>Edit</button>
                                <form method="POST" action="{{ route('admin.users.destroy', $user) }}" class="d-inline" onsubmit="return confirm('Hapus user {{ addslashes($user->name) }}?')">@csrf @method('DELETE')<button class="btn btn-sm bg-gradient-danger text-white rounded-pill mb-0" @disabled($user->is(auth()->user())) title="{{ $user->is(auth()->user()) ? 'Akun aktif tidak dapat dihapus' : 'Hapus user' }}" aria-label="Hapus user"><i class="ni ni-fat-remove" style="color:#fff!important;opacity:1!important;font-size:.8rem!important"></i></button></form>
                            </td>
                        </tr>
                    @empty<tr><td colspan="6" class="text-center text-secondary py-4">User tidak ditemukan.</td></tr>@endforelse
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-between align-items-center px-4 py-3 border-top"><span class="text-xs text-secondary">Menampilkan {{ $users->firstItem() ?? 0 }}-{{ $users->lastItem() ?? 0 }} dari {{ $users->total() }} user</span>{{ $users->links('vendor.pagination.modern') }}</div>
        </div>
    </div>
</div>

@include('admin.users.partials.form-modal', ['modalId'=>'createUserModal','title'=>'Tambah User','formId'=>'create-user-form','action'=>route('admin.users.store'),'method'=>'POST'])
@include('admin.users.partials.form-modal', ['modalId'=>'editUserModal','title'=>'Edit User','formId'=>'edit-user-form','action'=>'','method'=>'PUT'])
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded',function(){
    function toggleOffice(form){const role=form.querySelector('[name="role"]'),wrap=form.querySelector('.office-code-wrap'),input=form.querySelector('[name="kode_kantor"]');const branch=role.value==='cabang';wrap.style.display=branch?'block':'none';input.required=branch;if(!branch)input.value='';}
    document.querySelectorAll('.user-form').forEach(function(form){const role=form.querySelector('[name="role"]');role.addEventListener('change',function(){toggleOffice(form)});toggleOffice(form)});
    document.querySelectorAll('.edit-user').forEach(function(button){button.addEventListener('click',function(){const form=document.getElementById('edit-user-form');form.action=this.dataset.action;form.querySelector('[name="_user_id"]').value=this.dataset.userId;form.querySelector('[name="name"]').value=this.dataset.name||'';form.querySelector('[name="email"]').value=this.dataset.email||'';form.querySelector('[name="no_hp"]').value=this.dataset.noHp||'';form.querySelector('[name="role"]').value=this.dataset.role||'officer';form.querySelector('[name="kode_kantor"]').value=this.dataset.kodeKantor||'';form.querySelector('[name="password"]').value='';form.querySelector('[name="password_confirmation"]').value='';toggleOffice(form)})});
    @if($errors->any()) new bootstrap.Modal(document.getElementById('{{ old('_form') === 'edit' ? 'editUserModal' : 'createUserModal' }}')).show(); @endif
});
</script>
@endpush
