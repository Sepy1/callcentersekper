<aside class="sidenav navbar navbar-vertical navbar-expand-xs border-0 border-radius-xl my-3 fixed-start ms-3 bg-white" id="sidenav-main">
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
      <li class="nav-item">
        <a class="nav-link {{ Request::is('admin/users*') ? 'active bg-gradient-warning text-white' : '' }}" href="{{ route('admin.users') }}">
          <div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
            <i class="ni ni-single-02 text-lg" style="opacity:1!important"></i>
          </div>
          <span class="nav-link-text ms-1">Manajemen User</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link {{ (Request::is('admin/settings/*') || Request::is('admin/settings') || Request::is('admin/settings/sla') ? 'active bg-gradient-warning text-white' : '') }}" href="{{ url('admin/settings/sla') }}">
          <div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
            <i class="ni ni-settings text-lg opacity-10"></i>
          </div>
          <span class="nav-link-text ms-1">Pengaturan</span>
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

      {{-- CABANG --}}
      @if(auth()->check() && auth()->user()->role === 'cabang')
      <li class="nav-item">
        <a class="nav-link {{ Request::is('cabang/dashboard') ? 'active bg-gradient-warning text-white' : '' }}" href="{{ route('cabang.dashboard') }}">
          <div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
            <i class="ni ni-chart-bar-32 text-lg opacity-10"></i>
          </div>
          <span class="nav-link-text ms-1">Dashboard Cabang</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link {{ Request::is('cabang/tickets') ? 'active bg-gradient-warning text-white' : '' }}" href="{{ route('cabang.tickets') }}">
          <div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
            <i class="ni ni-bullet-list-67 text-lg opacity-10"></i>
          </div>
          <span class="nav-link-text ms-1">Daftar Tiket</span>
        </a>
      </li>
      @endif
      <li class="nav-item sidebar-appearance-item">
        <button class="nav-link w-100 border-0 bg-transparent text-start" type="button" id="appearance-menu-toggle" aria-expanded="false" aria-controls="appearance-menu-panel">
          <div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
            <svg class="appearance-menu-logo" width="17" height="17" viewBox="0 0 24 24" fill="none" aria-hidden="true">
              <path d="M12 3a9 9 0 1 0 0 18h1.15a1.85 1.85 0 0 0 1.35-3.12 1.84 1.84 0 0 1 1.34-3.1H18A3 3 0 0 0 21 11.8 9 9 0 0 0 12 3Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
              <circle cx="7.5" cy="11" r="1" fill="currentColor"/><circle cx="10" cy="7.5" r="1" fill="currentColor"/><circle cx="14.5" cy="7.5" r="1" fill="currentColor"/><circle cx="17" cy="11" r="1" fill="currentColor"/>
            </svg>
          </div>
          <span class="nav-link-text ms-1">Tema Aplikasi</span>
          <i class="fas fa-chevron-up ms-auto appearance-chevron"></i>
        </button>
        <div class="appearance-menu-panel" id="appearance-menu-panel" hidden>
          <div class="appearance-menu-title">Tema Aplikasi</div>
          <div class="appearance-theme-list" role="group" aria-label="Pilih tema aplikasi">
            <button type="button" class="appearance-theme-option" data-theme="default"><span class="theme-preview theme-preview--default"><i></i><b></b></span><span>Default</span><i class="fas fa-check theme-check"></i></button>
            <button type="button" class="appearance-theme-option" data-theme="indigo"><span class="theme-preview theme-preview--indigo"><i></i><b></b></span><span>Indigo</span><i class="fas fa-check theme-check"></i></button>
            <button type="button" class="appearance-theme-option" data-theme="navy"><span class="theme-preview theme-preview--navy"><i></i><b></b></span><span>Navy</span><i class="fas fa-check theme-check"></i></button>
            <button type="button" class="appearance-theme-option" data-theme="emerald"><span class="theme-preview theme-preview--emerald"><i></i><b></b></span><span>Emerald</span><i class="fas fa-check theme-check"></i></button>
            <button type="button" class="appearance-theme-option" data-theme="rose"><span class="theme-preview theme-preview--rose"><i></i><b></b></span><span>Rose</span><i class="fas fa-check theme-check"></i></button>
            <button type="button" class="appearance-theme-option" data-theme="amber"><span class="theme-preview theme-preview--amber"><i></i><b></b></span><span>Amber</span><i class="fas fa-check theme-check"></i></button>
            <button type="button" class="appearance-theme-option" data-theme="cyan"><span class="theme-preview theme-preview--cyan"><i></i><b></b></span><span>Cyan</span><i class="fas fa-check theme-check"></i></button>
          </div>
        </div>
      </li>
    </ul>
  </div>

  <style>
    #sidenav-main { background:var(--app-sidebar,#fff)!important; transition:background-color .2s ease; }
    #sidenav-collapse-main { height:calc(100vh - 145px)!important; max-height:none!important; overflow-y:auto!important; overflow-x:hidden!important; padding-bottom:1rem; }
    body,.main-content,.bg-gray-100 { background-color:var(--app-background,#f8f9fa)!important; transition:background-color .2s ease; }
    #sidenav-main .navbar-brand span,#sidenav-main .sidebar-user .fw-bold { color:var(--app-sidebar-text,#344767)!important; }
    #sidenav-main .navbar-nav .nav-link:not(.active) { color:var(--app-sidebar-text,#67748e)!important; }
    #sidenav-main .navbar-nav .nav-link.active,#sidenav-main .navbar-nav .nav-link.active.bg-gradient-warning { background:var(--app-sidebar-active,linear-gradient(180deg,#ffd56b 0%,#ff9a2a 100%))!important; }
    #sidenav-main .navbar-nav .nav-link .icon { background:var(--app-sidebar-icon-bg,#fff)!important; transition:background-color .2s ease; }
    #sidenav-main .navbar-nav .nav-link.active .icon { background:var(--app-sidebar-active-icon-bg,rgba(255,255,255,.18))!important; }
    #sidenav-main .navbar-nav .nav-link:not(.active) .icon i { color:var(--app-sidebar-icon,#344767)!important; opacity:1!important; }
    #sidenav-main .appearance-menu-logo { color:var(--app-sidebar-icon,#344767); transition:color .2s ease; }
    #sidenav-main .sidebar-user a[href*="/logout"] { color:var(--app-sidebar-text,#67748e)!important; opacity:.72; transition:opacity .18s ease,color .2s ease; }
    #sidenav-main .sidebar-user a[href*="/logout"]:hover { color:var(--app-sidebar-icon,#344767)!important; opacity:1; }
    #sidenav-main .horizontal { border-color:var(--app-sidebar-divider,rgba(0,0,0,.12)); }
    .sidebar-appearance-item { position:relative; margin-top:.35rem; }
    .appearance-chevron { font-size:.65rem; transition:transform .18s ease; }.sidebar-appearance-item.is-open .appearance-chevron{transform:rotate(180deg)}
    .appearance-menu-panel { max-height:none!important; overflow:visible!important; margin:0 0 .45rem; padding:.15rem .35rem; background:transparent; }
    .appearance-menu-title { margin:.1rem .25rem .3rem; color:var(--app-sidebar-text,#67748e); font-size:.58rem; font-weight:700; text-transform:uppercase; letter-spacing:.05em; opacity:.75; }
    .appearance-theme-list { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:.15rem; }.appearance-theme-option{display:flex;align-items:center;gap:.32rem;min-width:0;width:100%;padding:.34rem .3rem;border:0;border-radius:.45rem;background:transparent;color:var(--app-sidebar-text,#475569);font-size:.63rem;text-align:left}.appearance-theme-option>span:nth-child(2){overflow:hidden;text-overflow:ellipsis;white-space:nowrap}.appearance-theme-option:hover{background:rgba(128,138,160,.12)}.appearance-theme-option.is-active{background:rgba(109,74,255,.14);color:var(--app-sidebar-icon,#4f36c8);font-weight:700}.theme-check{display:none;margin-left:auto;font-size:.55rem}.appearance-theme-option.is-active .theme-check{display:block}
    .theme-preview{display:flex;width:22px;height:15px;flex:0 0 auto;border:1px solid rgba(128,138,160,.25);border-radius:4px;overflow:hidden;background:#fff}.theme-preview i{width:8px;background:var(--preview-sidebar)}.theme-preview b{width:6px;height:4px;margin:3px;border-radius:2px;background:var(--preview-active)}.theme-preview--default{--preview-sidebar:#fff;--preview-active:#ff9a2a}.theme-preview--indigo{--preview-sidebar:#241b5c;--preview-active:#6d4aff}.theme-preview--navy{--preview-sidebar:#12213d;--preview-active:#2584e8}.theme-preview--emerald{--preview-sidebar:#113d35;--preview-active:#24b47e}.theme-preview--rose{--preview-sidebar:#4c1930;--preview-active:#e44f87}.theme-preview--amber{--preview-sidebar:#4a3212;--preview-active:#f59e0b}.theme-preview--cyan{--preview-sidebar:#103b46;--preview-active:#16a6c7}
  </style>
  <script>
    (function(){
      const themes = {
        default:{sidebar:'#ffffff',active:'linear-gradient(180deg,#ffd56b 0%,#ff9a2a 100%)',ticketCard:'linear-gradient(145deg,#20285d 0%,#142039 100%)',icon:'#344767',iconBg:'#f5f6f8',activeIconBg:'rgba(255,255,255,.24)',text:'#67748e',heading:'#344767',divider:'rgba(0,0,0,.12)',background:'#f8f9fa'},
        indigo:{sidebar:'#241b5c',active:'#6d4aff',ticketCard:'linear-gradient(145deg,#352779 0%,#21184f 100%)',icon:'#b8adff',iconBg:'rgba(184,173,255,.12)',activeIconBg:'rgba(255,255,255,.18)',text:'#d9d5f2',heading:'#ffffff',divider:'rgba(255,255,255,.14)',background:'#f1effb'},
        navy:{sidebar:'#12213d',active:'#2584e8',ticketCard:'linear-gradient(145deg,#183b67 0%,#10243f 100%)',icon:'#80bfff',iconBg:'rgba(128,191,255,.12)',activeIconBg:'rgba(255,255,255,.18)',text:'#d1dbea',heading:'#ffffff',divider:'rgba(255,255,255,.14)',background:'#edf3f8'},
        emerald:{sidebar:'#113d35',active:'#24b47e',ticketCard:'linear-gradient(145deg,#175a4b 0%,#0e372f 100%)',icon:'#71dfbb',iconBg:'rgba(113,223,187,.12)',activeIconBg:'rgba(255,255,255,.18)',text:'#d2eee5',heading:'#ffffff',divider:'rgba(255,255,255,.14)',background:'#edf7f3'},
        rose:{sidebar:'#4c1930',active:'#e44f87',ticketCard:'linear-gradient(145deg,#71304b 0%,#43182b 100%)',icon:'#f4a6c2',iconBg:'rgba(244,166,194,.12)',activeIconBg:'rgba(255,255,255,.18)',text:'#f1d6e0',heading:'#ffffff',divider:'rgba(255,255,255,.14)',background:'#fbf0f4'},
        amber:{sidebar:'#4a3212',active:'#f59e0b',ticketCard:'linear-gradient(145deg,#6a491b 0%,#3f2a10 100%)',icon:'#f8cf72',iconBg:'rgba(248,207,114,.12)',activeIconBg:'rgba(255,255,255,.2)',text:'#f4e4c5',heading:'#ffffff',divider:'rgba(255,255,255,.14)',background:'#fbf6e9'},
        cyan:{sidebar:'#103b46',active:'#16a6c7',ticketCard:'linear-gradient(145deg,#176176 0%,#0d3946 100%)',icon:'#76d7e9',iconBg:'rgba(118,215,233,.12)',activeIconBg:'rgba(255,255,255,.18)',text:'#d0ebf0',heading:'#ffffff',divider:'rgba(255,255,255,.14)',background:'#edf8fa'}
      };
      function applyTheme(name){
        const selected=themes[name]||themes.emerald, root=document.documentElement;
        root.style.setProperty('--app-sidebar',selected.sidebar);root.style.setProperty('--app-sidebar-active',selected.active);root.style.setProperty('--app-ticket-card',selected.ticketCard);root.style.setProperty('--app-sidebar-icon',selected.icon);root.style.setProperty('--app-sidebar-icon-bg',selected.iconBg);root.style.setProperty('--app-sidebar-active-icon-bg',selected.activeIconBg);root.style.setProperty('--app-sidebar-text',selected.text);root.style.setProperty('--app-sidebar-divider',selected.divider);root.style.setProperty('--app-background',selected.background);
        document.querySelectorAll('#sidenav-main .navbar-brand span,#sidenav-main .sidebar-user .fw-bold').forEach(el=>el.style.setProperty('color',selected.heading,'important'));
        document.querySelectorAll('.appearance-theme-option').forEach(el=>el.classList.toggle('is-active',el.dataset.theme===name));
        try{localStorage.setItem('callcenter-theme',name)}catch(e){}
      }
      let saved='emerald';try{saved=localStorage.getItem('callcenter-theme')||'emerald'}catch(e){}
      applyTheme(saved);
      document.addEventListener('DOMContentLoaded',function(){
        applyTheme(saved);
        const toggle=document.getElementById('appearance-menu-toggle'),panel=document.getElementById('appearance-menu-panel'),item=document.querySelector('.sidebar-appearance-item');
        if(toggle&&panel){toggle.addEventListener('click',function(){const open=panel.hasAttribute('hidden');panel.toggleAttribute('hidden',!open);item.classList.toggle('is-open',open);toggle.setAttribute('aria-expanded',String(open));if(open){requestAnimationFrame(function(){item.scrollIntoView({block:'nearest',behavior:'smooth'})})}})}
        document.querySelectorAll('.appearance-theme-option').forEach(option=>option.addEventListener('click',function(){saved=this.dataset.theme;applyTheme(saved)}));
      });
    })();
  </script>
</aside>

{{-- floating notification bell (moved to partial) --}}
