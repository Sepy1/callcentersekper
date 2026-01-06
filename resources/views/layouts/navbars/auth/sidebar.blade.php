<aside class="sidenav navbar navbar-vertical navbar-expand-xs border-0 border-radius-xl my-3 fixed-start ms-3 bg-white" id="sidenav-main" style="background-color: #ffffff !important;">
  <div class="sidenav-header">
    <i class="fas fa-times p-3 cursor-pointer text-secondary opacity-5 position-absolute end-0 top-0 d-none d-xl-none" aria-hidden="true" id="iconSidenav"></i>
    <a class="align-items-center d-flex m-0 navbar-brand text-wrap" href="{{ route('dashboard') }}">
      <img src="/assets/img/logo.png" class="navbar-brand-img h-100" alt="...">
        <span class="ms-3 font-weight-bold">Call Center System</span>
    </a>
  </div>

  {{-- user info (show when authenticated) --}}
  @auth
  @php
    $u = auth()->user();
    $name = $u->name ?? '';
    $parts = preg_split('/\s+/', trim($name));
    $initials = '';
    foreach($parts as $p){ $initials .= strtoupper(substr($p,0,1)); if(strlen($initials) >= 2) break; }
  @endphp
  <div class="px-3 py-2 d-flex align-items-center sidebar-user">
    <div class="avatar avatar-sm bg-gradient-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width:40px;height:40px;">
      {{ $initials ?: 'U' }}
    </div>
    <div class="flex-grow-1 d-flex align-items-center justify-content-between">
        <div>
        <div class="fw-bold small mb-0 text-capitalize" title="{{ $name }}">{{ \Illuminate\Support\Str::limit($name, 22) }}</div>
        <a href="{{ url('/logout') }}" class="small text-decoration-none text-danger"><i class="fas fa-sign-out-alt me-1"></i>Logout</a>
      </div>

      {{-- notification moved to floating element (top-right) --}}
    </div>
  </div>
  @endauth

  <style>
    /* active menu highlight (yellow-orange / same tone as bg-gradient-warning) */
    #sidenav-main .navbar-nav .nav-link.active,
    #sidenav-main .navbar-nav .nav-link.active.bg-gradient-warning {
      background: linear-gradient(180deg,#ffd56b 0%,#ff9a2a 100%) !important;
      color: #fff !important;
      border-radius: .5rem;
      box-shadow: 0 4px 10px rgba(0,0,0,0.06);
    }
    #sidenav-main .navbar-nav .nav-link.active .nav-link-text,
    #sidenav-main .navbar-nav .nav-link.active svg,
    #sidenav-main .navbar-nav .nav-link.active i {
      color: #fff !important;
    }
    /* subtle contrast for the icon container when active */
    #sidenav-main .navbar-nav .nav-link.active .icon {
      background-color: rgba(255,255,255,0.10) !important;
    }

    /* hide Soft UI Configurator UI (fixed-plugin) */
    .fixed-plugin,
    #fixed-plugin,
    .soft-ui-configurator,
    .fixed-plugin-button,
    .fixed-plugin-btn {
      display: none !important;
      visibility: hidden !important;
      pointer-events: none !important;
    }

    /* floating notification bell */
    #floatingNotifBell {
      top: 16px;
      right: 16px;
      z-index: 2050;
    }
  </style>

  <script>
    // remove Soft UI Configurator nodes if present (extra safety)
    document.addEventListener('DOMContentLoaded', function(){
      try {
        const selectors = ['.fixed-plugin','#fixed-plugin','.soft-ui-configurator','.fixed-plugin-button','.fixed-plugin-btn'];
        selectors.forEach(sel => {
          document.querySelectorAll(sel).forEach(el => {
            if (el && el.parentNode) el.parentNode.removeChild(el);
          });
        });
      } catch(e) { /* ignore */ }
    });
  </script>
  <hr class="horizontal dark mt-0">
  <div class="collapse navbar-collapse  w-auto" id="sidenav-collapse-main">
    <ul class="navbar-nav">
     

      {{-- ADMIN --}}
      @if(auth()->check() && auth()->user()->role === 'admin')
      <li class="nav-item">
        <a class="nav-link {{ (Request::is('admin/dashboard-admin') ? 'active bg-gradient-warning text-white' : '') }}" href="{{ url('admin/dashboard-admin') }}">
          <div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
            <i class="ni ni-chart-bar-32 text-lg opacity-10"></i>
          </div>
          <span class="nav-link-text ms-1">Dasboard Admin</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link {{ (Request::is('admin/tickets') ? 'active bg-gradient-warning text-white' : '') }}" href="{{ url('admin/tickets') }}">
          <div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
            <i class="ni ni-bullet-list-67 text-lg opacity-10"></i>
          </div>
          <span class="nav-link-text ms-1">Daftar Tiket</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link {{ (Request::is('admin/tindak-lanjut') ? 'active bg-gradient-warning text-white' : '') }}" href="{{ url('admin/tindak-lanjut') }}">
          <div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
            <i class="ni ni-check-bold text-lg opacity-10"></i>
          </div>
          <span class="nav-link-text ms-1">Tindak Lanjut</span>
        </a>
      </li>
      @endif

      {{-- QA --}}
      @if(auth()->check() && auth()->user()->role === 'qa')
      <li class="nav-item">
        <a class="nav-link {{ (Request::is('qa/dashboard-qa') ? 'active bg-gradient-warning text-white' : '') }}" href="{{ url('qa/dashboard-qa') }}">
          <div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
            <i class="ni ni-chart-bar-32 text-lg opacity-10"></i>
          </div>
          <span class="nav-link-text ms-1">Dashboard QA</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link {{ (Request::is('qa/tickets') ? 'active bg-gradient-warning text-white' : '') }}" href="{{ url('qa/tickets') }}">
          <div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
            <i class="ni ni-bullet-list-67 text-lg opacity-10"></i>
          </div>
          <span class="nav-link-text ms-1">Daftar Tiket</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link {{ (Request::is('qa/tindak-lanjut') ? 'active bg-gradient-warning text-white' : '') }}" href="{{ url('qa/tindak-lanjut') }}">
          <div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
            <i class="ni ni-check-bold text-lg opacity-10"></i>
          </div>
          <span class="nav-link-text ms-1">Tindak Lanjut</span>
        </a>
      </li>
      @endif

      {{-- OFFICER --}}
      @if(auth()->check() && auth()->user()->role === 'officer')
      <li class="nav-item">
        <a class="nav-link {{ (Request::is('officer/dashboard-officer') ? 'active bg-gradient-warning text-white' : '') }}" href="{{ url('officer/dashboard-officer') }}">
          <div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
            <i class="ni ni-chart-bar-32 text-lg opacity-10"></i>
          </div>
          <span class="nav-link-text ms-1">Dashboard Officer</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link {{ (Request::is('officer/tickets') ? 'active bg-gradient-warning text-white' : '') }}" href="{{ url('officer/tickets') }}">
          <div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
            <i class="ni ni-bullet-list-67 text-lg opacity-10"></i>
          </div>
          <span class="nav-link-text ms-1">Daftar Tiket</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link {{ (Request::is('officer/tindak-lanjut') ? 'active bg-gradient-warning text-white' : '') }}" href="{{ url('officer/tindak-lanjut') }}">
          <div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
            <i class="ni ni-check-bold text-lg opacity-10"></i>
          </div>
          <span class="nav-link-text ms-1">Tindak Lanjut</span>
        </a>
      </li>
      @endif
     
      
      
    
        
</aside>

{{-- floating notification bell (moved to partial) --}}
