@extends('layouts.user_type.auth')

@section('content')
<div class="container-fluid py-4">
  <div class="card">
    <div class="card-body">
      <div class="row">
        <div class="col-lg-2 col-md-3 col-sm-6">
          <div class="card h-100">
            <div class="card-body p-3">
              <div class="row">
                <div class="col-8">
                  <div class="numbers">
                    <p class="text-sm mb-0 text-uppercase font-weight-bold">Tiket Open</p>
                    <h5 class="font-weight-bolder">{{ \App\Models\Ticket::where('status','open')->count() }}</h5>
                  </div>
                </div>
                <div class="col-4 text-end">
                  <div class="icon icon-shape bg-gradient-primary shadow text-center border-radius-md">
                    <i class="ni ni-folder-17 text-lg opacity-10" aria-hidden="true"></i>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="col-lg-2 col-md-3 col-sm-6">
          <div class="card h-100">
            <div class="card-body p-3">
              <div class="row">
                <div class="col-8">
                  <div class="numbers">
                    <p class="text-sm mb-0 text-uppercase font-weight-bold">In-Progress</p>
                    <h5 class="font-weight-bolder">{{ \App\Models\Ticket::where('status','in_progress')->count() }}</h5>
                  </div>
                </div>
                <div class="col-4 text-end">
                  <div class="icon icon-shape bg-gradient-warning shadow text-center border-radius-md">
                    <i class="ni ni-settings text-lg opacity-10" aria-hidden="true"></i>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="col-lg-2 col-md-3 col-sm-6">
          <div class="card h-100">
            <div class="card-body p-3">
              <div class="row">
                <div class="col-8">
                  <div class="numbers">
                    <p class="text-sm mb-0 text-uppercase font-weight-bold">Tiket Resolved</p>
                    <h5 class="font-weight-bolder">{{ \App\Models\Ticket::where('status','resolved')->count() }}</h5>
                  </div>
                </div>
                <div class="col-4 text-end">
                  <div class="icon icon-shape bg-gradient-success shadow text-center border-radius-md">
                    <i class="ni ni-check-bold text-lg opacity-10" aria-hidden="true"></i>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="col-lg-2 col-md-3 col-sm-6">
          <div class="card h-100">
            <div class="card-body p-3">
              <div class="row">
                <div class="col-8">
                  <div class="numbers">
                    <p class="text-sm mb-0 text-uppercase font-weight-bold">Tiket Closed</p>
                    <h5 class="font-weight-bolder">{{ \App\Models\Ticket::where('status','closed')->count() }}</h5>
                  </div>
                </div>
                <div class="col-4 text-end">
                  <div class="icon icon-shape bg-gradient-info shadow text-center border-radius-md">
                    <i class="ni ni-lock-circle-open text-lg opacity-10" aria-hidden="true"></i>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="col-lg-2 col-md-3 col-sm-6">
          <div class="card h-100">
            <div class="card-body p-3">
              <div class="row">
                <div class="col-8">
                  <div class="numbers">
                    <p class="text-sm mb-0 text-uppercase font-weight-bold">Tiket Rejected</p>
                    <h5 class="font-weight-bolder">{{ \App\Models\Ticket::where('status','rejected')->count() }}</h5>
                  </div>
                </div>
                <div class="col-4 text-end">
                  <div class="icon icon-shape bg-gradient-danger shadow text-center border-radius-md">
                    <i class="ni ni-fat-remove text-lg opacity-10" aria-hidden="true"></i>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="col-lg-2 col-md-3 col-sm-6">
          <div class="card h-100">
            <div class="card-body p-3 text-center">
              <h6 class="text-uppercase font-weight-bold mb-0">Waktu Sekarang</h6>
              <h5 class="font-weight-bolder" id="current-time"></h5>
              <p class="mb-0 text-muted" id="current-date"></p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- Card untuk QA: tiket yang sudah semua officer set ke proses_qa --}}
  <div class="row mt-4">
    <div class="col-lg-6 mb-lg-0 mb-4 d-flex">
      <div class="card h-100 w-100">
        <div class="card-body p-3 d-flex flex-column" style="height:100%;">
          <h6 class="text-uppercase font-weight-bold mb-3">Butuh Proses QA</h6>
          <div style="flex:1 1 auto; overflow:auto; position: relative; max-height: calc(100vh - 360px);">
            <table class="table table-hover table-sm">
              <thead class="sticky-top bg-white" style="z-index: 1;">
                <tr>
                  <th scope="col">#</th>
                  <th scope="col">Nomor Tiket</th>
                  <th scope="col">Status</th>
                  <th scope="col">Judul</th>
                  <th scope="col">Aksi</th>
                </tr>
              </thead>
              <tbody>
                @php
                  $tickets = \App\Models\Ticket::where('status', 'in_progress')
                    ->whereRaw("(select count(*) from ticket_officer where ticket_officer.ticket_id = tickets.id) > 0")
                    ->whereRaw("(select count(*) from ticket_officer where ticket_officer.ticket_id = tickets.id and LOWER(ticket_officer.status) = 'proses_qa') = (select count(*) from ticket_officer where ticket_officer.ticket_id = tickets.id)")
                    ->orderBy('created_at', 'desc')
                    ->get();
                @endphp

                @forelse($tickets as $index => $ticket)
                  <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $ticket->nomor_tiket }}</td>
                    <td>
                      <span class="badge bg-{{ $ticket->status === 'open' ? 'primary' : 'success' }}">
                        {{ ucfirst($ticket->status) }}
                      </span>
                    </td>
                    <td>{{ \Illuminate\Support\Str::limit($ticket->judul, 30) }}</td>
                    <td>
                      <a href="{{ url('qa/tindak-lanjut?ticket_id=' . $ticket->id) }}" class="btn btn-sm btn-warning">
                        Proses
                      </a>
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="5" class="text-center text-muted">Tidak ada tiket</td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    <div class="col-lg-6">
      <div class="card">
        <div class="card-body p-3">
          <h6 class="text-uppercase font-weight-bold mb-3">Laporan Tiket</h6>
          <form id="filter-form" class="mb-3">
            <div class="row">
              <div class="col-md-6">
                <label for="start_date" class="form-label">Dari Tanggal</label>
                <input type="date" id="start_date" name="start_date" class="form-control" value="{{ request('start_date') }}">
              </div>
              <div class="col-md-6">
                <label for="end_date" class="form-label">Sampai Tanggal</label>
                <input type="date" id="end_date" name="end_date" class="form-control" value="{{ request('end_date') }}">
              </div>
            </div>
            <div class="d-flex gap-2 mt-3">
              <button type="button" id="filter-button" class="btn btn-primary btn-sm">Terapkan</button>
              <button type="button" id="download-nominatif" class="btn btn-outline-secondary btn-sm">Download Nominatif</button>
              <button type="button" id="generate-pdf" class="btn btn-outline-secondary btn-sm">Generate PDF</button>
            </div>
          </form>
          <!-- Resilient fallback: attach a minimal click handler that fetches chart JSON and shows a summary.
               This runs even if other page scripts fail, because it's a small self-contained block. -->
          <script>
            (function(){
              try {
                var btn = document.getElementById('filter-button');
                if (!btn) return;

                // helper: render chart given data (labels, values)
                function renderChartWithData(data) {
                  try {
                    var canvas = document.getElementById('ticket-chart');
                    if (!canvas) return console.error('Canvas not found');
                    var ctx = canvas.getContext('2d');
                    // ensure canvas visible
                    canvas.style.display = 'block';
                    var container = canvas.parentNode;
                    var ph = container ? container.querySelector('#chart-placeholder') : null;
                    if (ph) ph.style.display = 'none';
                    // destroy previous
                    if (window._qaChart) { try { window._qaChart.destroy(); } catch(e){} }
                    var gradient = ctx.createLinearGradient(0,0,0,200);
                    gradient.addColorStop(0,'rgba(255,159,64,0.24)');
                    gradient.addColorStop(1,'rgba(255,159,64,0.00)');
                    window._qaChart = new Chart(ctx, {
                      type: 'line',
                      data: { labels: data.labels || [], datasets: [{ label: 'Jumlah Tiket', data: data.values || [], tension:0.4, borderColor:'rgba(255,159,64,1)', backgroundColor:gradient, fill:true, pointRadius:0, pointHoverRadius:6, borderWidth:2 }] },
                      options: { responsive:true, maintainAspectRatio:false, plugins:{legend:{display:false}}, interaction:{mode:'index', intersect:false}, scales:{x:{grid:{display:false}, ticks:{maxRotation:0, autoSkip:true}}, y:{grid:{drawBorder:false}, beginAtZero:true, ticks:{stepSize:1}} } }
                    });
                    console.log('QA fallback chart rendered');
                  } catch(err) { console.error('renderChartWithData error', err); }
                }

                function loadChartJsThenRender(data) {
                  if (typeof Chart !== 'undefined') { renderChartWithData(data); return; }
                  var s = document.createElement('script');
                  s.src = 'https://cdn.jsdelivr.net/npm/chart.js';
                  s.onload = function(){ setTimeout(function(){ renderChartWithData(data); }, 10); };
                  s.onerror = function(){ console.error('Failed to load Chart.js'); alert('Gagal memuat Chart.js'); };
                  document.head.appendChild(s);
                }

                btn.addEventListener('click', function(ev){
                  ev.preventDefault && ev.preventDefault();
                  var s = (document.getElementById('start_date')||{}).value || '';
                  var e = (document.getElementById('end_date')||{}).value || '';
                  var url = '{{ url('/') }}' + '/admin/tickets/chart-data?start_date=' + encodeURIComponent(s) + '&end_date=' + encodeURIComponent(e);
                  fetch(url, { credentials: 'same-origin' }).then(function(r){ if(!r.ok) throw new Error('network'); return r.json() }).then(function(data){
                    try {
                      // if Chart.js available, render directly; otherwise load it then render
                      loadChartJsThenRender(data);
                    } catch(e){ console.error(e); }
                  }).catch(function(err){ console.error('fetch chart-data failed', err); alert('Gagal mengambil data grafik. Lihat console.'); });
                });
              } catch(e) { console.error('fallback handler attach failed', e); }
            })();
          </script>
          <script>
            // Actions for Download Nominatif / Generate PDF from QA dashboard
            document.addEventListener('DOMContentLoaded', function(){
              const downloadBtn = document.getElementById('download-nominatif');
              const pdfBtn = document.getElementById('generate-pdf');
              function buildQueryUrl(path){
                const s = (document.getElementById('start_date')||{}).value || '';
                const e = (document.getElementById('end_date')||{}).value || '';
                return path + '?start_date=' + encodeURIComponent(s) + '&end_date=' + encodeURIComponent(e);
              }
              if (downloadBtn){
                downloadBtn.addEventListener('click', function(){
                  const url = buildQueryUrl('{{ url('/admin/tickets/download-nominatif') }}');
                  window.location.href = url;
                });
              }
              if (pdfBtn){
                pdfBtn.addEventListener('click', function(){
                  const url = buildQueryUrl('{{ url('/admin/tickets/generate-pdf') }}');
                  window.open(url, '_blank');
                });
              }
            });
          </script>
          <div class="chart">
            <canvas id="ticket-chart" class="chart-canvas" height="200"></canvas>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
  (function initQaDashboard(){
    function run(){
      try {
        const canvas = document.getElementById('ticket-chart');
        if (!canvas) return;
        const ctx = canvas.getContext('2d');
        let chart;
        var baseUrl = "{{ url('/') }}";
        const chartContainer = canvas.parentNode;
        let placeholder = chartContainer.querySelector('#chart-placeholder');
        if (!placeholder) {
          placeholder = document.createElement('div');
          placeholder.id = 'chart-placeholder';
          placeholder.className = 'text-center text-muted';
          placeholder.style.padding = '40px';
          placeholder.style.display = 'none';
          chartContainer.appendChild(placeholder);
        }

        function fetchChartData(startDate, endDate) {
          var url = baseUrl + '/admin/tickets/chart-data?start_date=' + encodeURIComponent(startDate) + '&end_date=' + encodeURIComponent(endDate);
          fetch(url)
            .then(response => response.ok ? response.json() : Promise.reject())
            .then(data => {
              const sum = (data.values || []).reduce((s, v) => s + (parseInt(v) || 0), 0);
              if (!data.labels || data.labels.length === 0 || sum === 0) {
                try { if (chart) { chart.destroy(); chart = null; } } catch(e){}
                canvas.style.display = 'none';
                placeholder.textContent = 'Tidak ada data untuk periode ini';
                placeholder.style.display = 'block';
                return;
              }

              placeholder.style.display = 'none';
              canvas.style.display = 'block';

              function renderChart(data) {
                try {
                  if (chart) { try { chart.destroy(); } catch(e){} }
                  const gradient = ctx.createLinearGradient(0, 0, 0, 200);
                  gradient.addColorStop(0, 'rgba(255, 159, 64, 0.24)');
                  gradient.addColorStop(1, 'rgba(255, 159, 64, 0.00)');
                  chart = new Chart(ctx, {
                    type: 'line',
                    data: { labels: data.labels, datasets: [{ label: 'Jumlah Tiket', data: data.values, tension:0.4, borderColor:'rgba(255,159,64,1)', backgroundColor:gradient, fill:true, pointRadius:0, pointHoverRadius:6, borderWidth:2 }] },
                    options: { responsive:true, maintainAspectRatio:false, plugins:{legend:{display:false}}, interaction:{mode:'index', intersect:false}, scales:{x:{grid:{display:false}, ticks:{maxRotation:0, autoSkip:true}}, y:{grid:{drawBorder:false}, beginAtZero:true, ticks:{stepSize:1}} } }
                  });
                } catch (err) { console.error('renderChart error', err); }
              }

              if (typeof Chart !== 'undefined') {
                renderChart(data);
              } else {
                var s = document.createElement('script');
                s.src = 'https://cdn.jsdelivr.net/npm/chart.js';
                s.onload = function(){ setTimeout(function(){ renderChart(data); }, 10); };
                s.onerror = function(){ console.error('Failed to load Chart.js'); };
                document.head.appendChild(s);
              }
            }).catch(()=>{ try { if(chart) { chart.destroy(); } ctx && ctx.clearRect && ctx.clearRect(0,0,ctx.canvas.width, ctx.canvas.height); } catch(e){} });
        }

        const filterBtn = document.getElementById('filter-button');
        if (filterBtn) {
          filterBtn.addEventListener('click', function () {
            const startDate = document.getElementById('start_date').value;
            const endDate = document.getElementById('end_date').value;
            fetchChartData(startDate, endDate);
          });
        }

        const today = new Date().toISOString().split('T')[0];
        const lastMonth = new Date(); lastMonth.setDate(lastMonth.getDate() - 30);
        const defaultStartDate = lastMonth.toISOString().split('T')[0];
        fetchChartData(defaultStartDate, today);
      } catch (e) {
        console.error('QA dashboard init error', e);
      }
    }

    if (document.readyState !== 'loading') {
      run();
    } else {
      document.addEventListener('DOMContentLoaded', run);
      // fallback in case DOMContentLoaded was missed
      setTimeout(function(){ if(document.readyState !== 'loading') run(); }, 300);
    }
  })();
</script>
<script>
  // Script untuk menampilkan waktu dan tanggal di Dashboard QA
  document.addEventListener('DOMContentLoaded', function () {
    function updateTime() {
      const now = new Date();
      const optionsDate = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
      const optionsTime = { hour: '2-digit', minute: '2-digit', second: '2-digit' };

      const time = now.toLocaleTimeString('id-ID', optionsTime);
      const date = now.toLocaleDateString('id-ID', optionsDate);

      const tEl = document.getElementById('current-time');
      const dEl = document.getElementById('current-date');
      if (tEl) tEl.textContent = time;
      if (dEl) dEl.textContent = date;
    }

    updateTime();
    setInterval(updateTime, 1000);
  });
</script>
@endsection
