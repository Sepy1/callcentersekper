@extends('layouts.user_type.auth')

@section('content')
<div class="container-fluid py-4">
    <div class="card">
        <div class="card-header">
            <h6>Detail Tiket</h6>
        </div>
        <div class="card-body">
            @include('admin.tickets.partials.detail')
        </div>
    </div>
</div>
@endsection