<!--
=========================================================
* Soft UI Dashboard - v1.0.3
=========================================================

* Product Page: https://www.creative-tim.com/product/soft-ui-dashboard
* Copyright 2021 Creative Tim (https://www.creative-tim.com)
* Licensed under MIT (https://www.creative-tim.com/license)

* Coded by Creative Tim

=========================================================

* The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
-->
<!DOCTYPE html>

@if (\Request::is('rtl'))
  <html dir="rtl" lang="ar" class="page-transition">
@else
  <html lang="en" class="page-transition">
@endif

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

  @if (env('IS_DEMO'))
      <x-demo-metas></x-demo-metas>
  @endif

  
  <link rel="apple-touch-icon" sizes="180x180" href="/assets/img/logo-ct.png">
  <link rel="icon" type="image/png" sizes="32x32" href="/assets/img/logo-ct.png">
  <link rel="icon" type="image/png" sizes="16x16" href="/assets/img/logo-ct.png">
  <link rel="shortcut icon" href="/assets/img/logo-ct.png">
  <link rel="icon" href="/favicon.ico">
  <title>
    Call Center PT BPR BKK Jateng
  </title>
  <!--     Fonts and icons     -->
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet" />
  <!-- Nucleo Icons -->
  <link href="{{ asset('assets/css/nucleo-icons.css') }}" rel="stylesheet" />
  <link href="{{ asset('assets/css/nucleo-svg.css') }}" rel="stylesheet" />
  <!-- Font Awesome Icons -->
  <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>
  <link href="{{ asset('assets/css/nucleo-svg.css') }}" rel="stylesheet" />
  <!-- CSS Files -->
  <link id="pagestyle" href="{{ asset('assets/css/soft-ui-dashboard.css') }}?v=1.0.3" rel="stylesheet" />

  {{-- CSRF token for AJAX requests --}}
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <style>
    html { font-size: 80%; }
    html, body { min-height: 100%; overflow-x: hidden; }
    body {
      -webkit-font-smoothing: antialiased;
      -moz-osx-font-smoothing: grayscale;
    }
    html.page-transition { opacity: 0; transition: opacity 320ms ease-in-out; }
    html.page-transition.is-visible { opacity: 1; }
  </style>
</head>

<body class="g-sidenav-show  bg-gray-100 {{ (\Request::is('rtl') ? 'rtl' : (Request::is('virtual-reality') ? 'virtual-reality' : '')) }} ">
  @auth
    @yield('auth')
  @endauth
  @guest
    @yield('guest')
  @endguest

  @if(session()->has('success'))
    <div x-data="{ show: true}"
        x-init="setTimeout(() => show = false, 4000)"
        x-show="show"
        class="position-fixed bg-success rounded right-3 text-sm py-2 px-4">
      <p class="m-0">{{ session('success')}}</p>
    </div>
  @endif

  {{-- include floating notification bell partial (hide on login/session/forgot/reset pages) --}}
  @if (!Request::is('session') && !Request::is('login') && !Request::is('*forgot*') && !Request::is('*reset*'))
    @include('partials.notification_bell')
  @endif

    <!--   Core JS Files   -->
  <script src="{{ asset('assets/js/core/popper.min.js') }}"></script>
  <script src="{{ asset('assets/js/core/bootstrap.min.js') }}"></script>
  <script src="{{ asset('assets/js/plugins/perfect-scrollbar.min.js') }}"></script>
  <script src="{{ asset('assets/js/plugins/smooth-scrollbar.min.js') }}"></script>
  <script src="{{ asset('assets/js/plugins/fullcalendar.min.js') }}"></script>
  <script src="{{ asset('assets/js/plugins/chartjs.min.js') }}"></script>
  @stack('rtl')
  @stack('dashboard')
  <script>
    var win = navigator.platform.indexOf('Win') > -1;
    if (win && document.querySelector('#sidenav-scrollbar')) {
      var options = {
        damping: '0.5'
      }
      Scrollbar.init(document.querySelector('#sidenav-scrollbar'), options);
    }
  </script>

  <!-- Github buttons -->
  <script async defer src="https://buttons.github.io/buttons.js"></script>
  <!-- Control Center for Soft Dashboard: parallax effects, scripts for the example pages etc -->
  <script src="{{ asset('assets/js/soft-ui-dashboard.min.js') }}?v=1.0.3"></script>
  <script>
    (function(){
      var duration = 320;
      function isLocalLink(a){
        if(!a) return false;
        var href = a.getAttribute('href') || '';
        if(!href) return false;
        if(href.indexOf('mailto:') === 0 || href.indexOf('tel:') === 0 || href.indexOf('#') === 0) return false;
        if(a.target && a.target === '_blank') return false;
        try { var url = new URL(href, location.href); return url.origin === location.origin; } catch(e){ return false; }
      }

      function showPage(){ document.documentElement.classList.add('is-visible'); }
      function hidePage(){ document.documentElement.classList.remove('is-visible'); }

      // DOMContentLoaded covers normal navigations
      document.addEventListener('DOMContentLoaded', function(){
        showPage();
      }, false);

      // pageshow covers back/forward cache restores (bfcache)
      window.addEventListener('pageshow', function(e){
        // when navigating via back/forward, some browsers restore page from cache without firing DOMContentLoaded
        // ensure visibility is restored
        showPage();
        if (e && e.persisted) {
          // force a reflow to ensure transitions apply
          void document.documentElement.offsetWidth;
        }
      }, false);

      // intercept same-origin link clicks to fade out before navigating
      document.body.addEventListener('click', function(e){
        var a = e.target.closest('a');
        if(!a) return;
        if(!isLocalLink(a)) return;
        e.preventDefault();
        hidePage();
        setTimeout(function(){ window.location.href = new URL(a.getAttribute('href'), location.href).href; }, duration);
      }, true);

      // fade on form submit
      document.body.addEventListener('submit', function(){ hidePage(); }, true);
    })();
  </script>
</body>

</html>
