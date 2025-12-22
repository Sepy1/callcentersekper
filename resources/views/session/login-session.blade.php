@extends('layouts.user_type.guest')

@section('content')

<style>
/* ===== FULL BACKGROUND LOGIN ===== */
.login-bg {
    background: url('../assets/img/cc.jpg') no-repeat center center;
    background-size: cover;
    position: relative;
}

/* overlay gelap agar card lebih kontras */
.login-bg::before {
    content: "";
    position: absolute;
    inset: 0;
    background: rgba(0, 0, 0, 0.35);
    z-index: 0;
}

/* pastikan konten di atas overlay */
.login-bg > .container {
    position: relative;
    z-index: 1;
}
</style>

<style>
/* ===== GLASSMORPHISM CARD ===== */
.glass-card {
    background: #ffffff; /* PUTIH BERSIH */
    border-radius: 1.5rem;
    border: 1px solid rgba(255, 255, 255, 0.6);
    box-shadow:
        0 25px 45px rgba(0, 0, 0, 0.18);
    animation: fadeUp 0.8s ease forwards;
}

/* ===== FADE IN ANIMATION ===== */
@keyframes fadeUp {
    from {
        opacity: 0;
        transform: translateY(30px) scale(0.97);
    }
    to {
        opacity: 1;
        transform: translateY(0) scale(1);
    }
}

/* ===== INPUT ANIMATION ===== */
.form-control {
    transition: all 0.25s ease;
}

.form-control:focus {
    transform: translateY(-1px);
    box-shadow: 0 8px 20px rgba(23, 162, 184, 0.25);
    border-color: #17c1e8;
}

/* ===== BUTTON ANIMATION ===== */
.btn-animated {
    position: relative;
    overflow: hidden;
    transition: all 0.25s ease;
}

.btn-animated:hover {
    transform: translateY(-2px);
    box-shadow: 0 12px 25px rgba(23, 162, 184, 0.35);
}

.btn-animated:active {
    transform: translateY(0);
    box-shadow: 0 6px 15px rgba(23, 162, 184, 0.25);
}

/* ===== RIPPLE EFFECT ===== */
.btn-animated::after {
    content: "";
    position: absolute;
    top: 50%;
    left: 50%;
    width: 10px;
    height: 10px;
    background: rgba(255,255,255,0.5);
    border-radius: 50%;
    transform: translate(-50%, -50%);
    opacity: 0;
}

.btn-animated:active::after {
    animation: ripple 0.6s ease;
}

@keyframes ripple {
    from {
        opacity: 0.8;
        width: 10px;
        height: 10px;
    }
    to {
        opacity: 0;
        width: 300px;
        height: 300px;
    }
}
</style>

<div class="d-flex flex-column min-vh-100">

    <main class="main-content mt-0 flex-grow-1">
        <section>
            <div class="page-header min-vh-100 login-bg d-flex align-items-center">

                <div class="container">
                    <div class="row align-items-center">

                        {{-- LOGIN CARD --}}
                        <div class="col-xl-4 col-lg-5 col-md-6 mx-auto">
                           <div class="card glass-card mt-8">

                                {{-- HEADER --}}
                                <div class="card-header bg-transparent text-center pt-4 pb-2">
                                    <h3 class="font-weight-bolder text-info text-gradient mb-1">
                                        Selamat Datang
                                    </h3>
                                    <p class="text-sm text-muted mb-0">
                                        Call Center System
                                    </p>
                                </div>

                                {{-- BODY --}}
                                <div class="card-body px-4 py-4">
                                    <form role="form" method="POST" action="/session">
                                        @csrf

                                        <label class="text-sm">Email</label>
                                        <div class="mb-3">
                                            <input type="email"
                                                   class="form-control"
                                                   name="email"
                                                   id="email"
                                                   placeholder="Email"
                                                   value="">
                                            @error('email')
                                                <p class="text-danger text-xs mt-2">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        <label class="text-sm">Password</label>
                                        <div class="mb-3">
                                            <input type="password"
                                                   class="form-control"
                                                   name="password"
                                                   id="password"
                                                   placeholder="Password"
                                                   value="">
                                            @error('password')
                                                <p class="text-danger text-xs mt-2">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        <div class="form-check form-switch mb-3">
                                            <input class="form-check-input" type="checkbox" id="rememberMe" checked>
                                            <label class="form-check-label text-sm" for="rememberMe">
                                                Simpan Login
                                            </label>
                                        </div>

                                        <button type="submit" class="btn bg-gradient-info w-100 mt-3 btn-animated">
    Masuk
</button>
                                    </form>
                                </div>

                                {{-- FOOTER CARD --}}
                                <div class="card-footer text-center py-3">
                                    <small class="text-muted">
                                        Reset Password?
                                        <a href="/login/forgot-password"
                                           class="text-info text-gradient font-weight-bold">
                                            Klik Disini
                                        </a>
                                    </small>
                                </div>

                            </div>
                        </div>

                        {{-- IMAGE RIGHT --}}
                       

                    </div>
                </div>

            </div>
        </section>
    </main>


</div>

@endsection
