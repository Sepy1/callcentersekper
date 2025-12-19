<!-- FLOATING NOTIFICATION BELL -->
<div id="floatingNotifBell"
     class="position-fixed"
     style="top:16px; right:16px; z-index:1040;">

  <div class="dropdown">

    <a
      class="text-decoration-none position-relative d-inline-flex align-items-center p-2 bg-white rounded shadow-sm"
      href="#"
      id="floatingNotifDropdown"
      data-bs-toggle="dropdown"
      aria-expanded="false"
      style="color:#495057;"
    >
      <!-- INLINE SVG BELL -->
      <svg xmlns="http://www.w3.org/2000/svg"
           width="20"
           height="20"
           viewBox="0 0 24 24"
           fill="none"
           stroke="currentColor"
           stroke-width="1.6"
           stroke-linecap="round"
           stroke-linejoin="round"
           aria-hidden="true"
           focusable="false"
           style="display:block;">
        <path d="M15 17h5l-1.405-1.405A2.032 2.032 0 0 1 18 14.158V11c0-3.07-1.64-5.64-4.5-6.32V4a1.5 1.5 0 0 0-3 0v.68C7.64 5.36 6 7.92 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h11z"></path>
        <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
      </svg>

      {{-- BADGE --}}
      @if(isset($sidebar_notifications_unread_count) && $sidebar_notifications_unread_count > 0)
        <span class="badge bg-danger rounded-pill"
              style="position:absolute; top:-6px; right:-6px; font-size:10px;">
          {{ $sidebar_notifications_unread_count }}
        </span>
      @endif
    </a>

    <!-- DROPDOWN -->
    <ul class="dropdown-menu dropdown-menu-end p-2 shadow"
        aria-labelledby="floatingNotifDropdown"
        style="min-width:320px; max-height:260px; overflow:auto;">

      <li class="small fw-bold mb-2 ps-2">Notifications</li>

      @if(!empty($sidebar_notifications) && $sidebar_notifications->count())
        @foreach($sidebar_notifications->take(5) as $n)
          <li>
            <a
              class="dropdown-item notif-item d-flex justify-content-between align-items-start {{ $n->is_read ? '' : 'bg-light' }}"
              href="{{ $n->link ?: url('notifications') }}"
              data-notif-id="{{ $n->id }}"
              data-notif-link="{{ $n->link ?: url('notifications') }}"
              style="white-space:normal;"
            >
              <div>
                <div class="fw-bold small mb-0">
                  {{ \Illuminate\Support\Str::limit($n->title ?? 'No title', 80) }}
                </div>
                <div class="text-muted small">
                  {{ \Illuminate\Support\Str::limit($n->message ?? '', 120) }}
                </div>
              </div>
              <div class="text-muted text-end small ms-2" style="min-width:70px;">
                {{ $n->created_at->diffForHumans() }}
              </div>
            </a>
          </li>
        @endforeach

      @else
        <li class="px-3 py-2 text-center small text-muted">
          No notifications
        </li>
      @endif

    </ul>
  </div>
</div>

<script>
  // POST mark-read then redirect. Uses meta csrf-token.
  (function(){
    function getCsrf() {
      var m = document.querySelector('meta[name="csrf-token"]');
      return m ? m.getAttribute('content') : '';
    }

    // use absolute base URL to avoid path issues
    var markReadBase = '{{ url('/notifications/mark-read') }}';

    document.addEventListener('click', function(e){
      var el = e.target;
      // find closest .notif-item anchor
      while (el && el !== document) {
        if (el.matches && el.matches('.notif-item')) break;
        el = el.parentNode;
      }
      if (!el || el === document) return;
      e.preventDefault();
      var id = el.getAttribute('data-notif-id');
      var link = el.getAttribute('data-notif-link') || el.getAttribute('href') || '/notifications';
      if (!id) { window.location = link; return; }

      fetch(markReadBase + '/' + encodeURIComponent(id), {
         method: 'POST',
         headers: {
           'Content-Type': 'application/json',
           'X-CSRF-TOKEN': getCsrf(),
           'X-Requested-With': 'XMLHttpRequest',
           'Accept': 'application/json'
         },
         credentials: 'same-origin',
         body: JSON.stringify({})
       }).then(function(resp){
         // if unauthorized (401) server will return JSON — still navigate but log
         if (resp.ok) {
           // optionally update badge UI here (page reload will reflect accurate count)
         }
         window.location = link;
       }).catch(function(){
         // fallback: still navigate
         window.location = link;
       });
    }, false);
  })();
</script>
