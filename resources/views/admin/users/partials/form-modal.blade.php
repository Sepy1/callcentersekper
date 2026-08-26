@php($restoreOld = old('_form') === ($method === 'POST' ? 'create' : 'edit'))
@php($formAction = $method === 'PUT' && $restoreOld && old('_user_id') ? url('admin/users/' . old('_user_id')) : $action)
<div class="modal fade" id="{{ $modalId }}" tabindex="-1" aria-hidden="true"><div class="modal-dialog modal-dialog-centered"><div class="modal-content">
    <form id="{{ $formId }}" class="user-form" method="POST" action="{{ $formAction }}">@csrf @if($method !== 'POST') @method($method) @endif
        <input type="hidden" name="_form" value="{{ $method === 'POST' ? 'create' : 'edit' }}">
        @if($method === 'PUT')<input type="hidden" name="_user_id" value="{{ $restoreOld ? old('_user_id') : '' }}">@endif
        <div class="modal-header"><h6 class="modal-title">{{ $title }}</h6><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
        <div class="modal-body"><div class="row g-3">
            <div class="col-12"><label class="form-label text-sm">Nama</label><input class="form-control" name="name" value="{{ $restoreOld ? old('name') : '' }}" required></div>
            <div class="col-12"><label class="form-label text-sm">Email</label><input type="email" class="form-control" name="email" value="{{ $restoreOld ? old('email') : '' }}" required></div>
            <div class="col-md-6"><label class="form-label text-sm">No. HP WhatsApp</label><input class="form-control" name="no_hp" value="{{ $restoreOld ? old('no_hp') : '' }}" placeholder="628xxxxxxxxxx"></div>
            <div class="col-md-6"><label class="form-label text-sm">Role</label><select class="form-select" name="role" required>@foreach(['admin','qa','officer','cabang'] as $role)<option value="{{ $role }}" @selected(($restoreOld ? old('role','officer') : 'officer') === $role)>{{ ucfirst($role) }}</option>@endforeach</select></div>
            <div class="col-12 office-code-wrap" style="display:none"><label class="form-label text-sm">Kode Kantor</label><input class="form-control" name="kode_kantor" value="{{ $restoreOld ? old('kode_kantor') : '' }}"></div>
            <div class="col-md-6"><label class="form-label text-sm">Password {{ $method === 'PUT' ? '(opsional)' : '' }}</label><input type="password" class="form-control" name="password" {{ $method === 'POST' ? 'required' : '' }} minlength="8"></div>
            <div class="col-md-6"><label class="form-label text-sm">Konfirmasi Password</label><input type="password" class="form-control" name="password_confirmation" {{ $method === 'POST' ? 'required' : '' }} minlength="8"></div>
        </div></div>
        <div class="modal-footer"><button type="button" class="btn btn-sm btn-light mb-0" data-bs-dismiss="modal">Batal</button><button class="btn btn-sm bg-gradient-success mb-0">Simpan</button></div>
    </form>
</div></div></div>
