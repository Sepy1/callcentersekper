@extends('layouts.user_type.auth')

@section('content')
<style>
  .need-process-table-wrap {
    flex: 1 1 auto;
    position: relative;
    max-height: calc(100vh - 360px);
    overflow-y: auto;
    overflow-x: hidden;
  }
  .need-process-table {
    width: 100%;
    table-layout: fixed;
    font-size: 0.8rem;
  }
  .need-process-table th,
  .need-process-table td {
    padding: 0.35rem 0.45rem;
    vertical-align: middle;
  }
  .need-process-table .col-no { width: 8%; }
  .need-process-table .col-ticket { width: 24%; }
  .need-process-table .col-status { width: 15%; }
  .need-process-table .col-title { width: 35%; }
  .need-process-table .col-action { width: 18%; }
  .need-process-table td:nth-child(2),
  .need-process-table td:nth-child(4) {
    white-space: normal;
    word-break: break-word;
  }
  .need-process-table .btn {
    font-size: 0.72rem;
    padding: 0.25rem 0.5rem;
    white-space: nowrap;
  }
</style>
<div class="container-fluid py-4">
  {{-- Card besar untuk membungkus semua card kecil --}}
  <div class="card">
    <div class="card-body">
      <div class="row">
        {{-- Jumlah Tiket Open --}}
        <div class="col-lg-2 col-md-3 col-sm-6">
          <div class="card h-100">
            <div class="card-body p-3">
              <div class="row">
                <div class="col-8">
                  <div class="numbers">
                    <p class="text-sm mb-0 text-uppercase font-weight-bold">Tiket Open</p>
                    <h5 class="font-weight-bolder">{{ \App\Models\Ticket::whereHas('officers', function($q){ $q->where('ticket_officer.officer_id', auth()->id()); })->where('status', 'open')->count() }}</h5>
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

        {{-- Jumlah Tiket In-Progress --}}
        <div class="col-lg-2 col-md-3 col-sm-6">
          <div class="card h-100">
            <div class="card-body p-3">
              <div class="row">
                <div class="col-8">
                  <div class="numbers">
                    <p class="text-sm mb-0 text-uppercase font-weight-bold">In-Progress</p>
                    <h5 class="font-weight-bolder">{{ \App\Models\Ticket::whereHas('officers', function($q){ $q->where('ticket_officer.officer_id', auth()->id()); })->where('status', 'in_progress')->count() }}</h5>
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

        {{-- Jumlah Tiket Resolved --}}
        <div class="col-lg-2 col-md-3 col-sm-6">
          <div class="card h-100">
            <div class="card-body p-3">
              <div class="row">
                <div class="col-8">
                  <div class="numbers">
                    <p class="text-sm mb-0 text-uppercase font-weight-bold">Tiket Resolved</p>
                    <h5 class="font-weight-bolder">{{ \App\Models\Ticket::whereHas('officers', function($q){ $q->where('ticket_officer.officer_id', auth()->id()); })->where('status', 'resolved')->count() }}</h5>
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

        {{-- Jumlah Tiket Closed --}}
        <div class="col-lg-2 col-md-3 col-sm-6">
          <div class="card h-100">
            <div class="card-body p-3">
              <div class="row">
                <div class="col-8">
                  <div class="numbers">
                    <p class="text-sm mb-0 text-uppercase font-weight-bold">Tiket Closed</p>
                    <h5 class="font-weight-bolder">{{ \App\Models\Ticket::whereHas('officers', function($q){ $q->where('ticket_officer.officer_id', auth()->id()); })->where('status', 'closed')->count() }}</h5>
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

        {{-- Jumlah Tiket Rejected --}}
        <div class="col-lg-2 col-md-3 col-sm-6">
          <div class="card h-100">
            <div class="card-body p-3">
              <div class="row">
                <div class="col-8">
                  <div class="numbers">
                    <p class="text-sm mb-0 text-uppercase font-weight-bold">Tiket Rejected</p>
                    <h5 class="font-weight-bolder">{{ \App\Models\Ticket::whereHas('officers', function($q){ $q->where('ticket_officer.officer_id', auth()->id()); })->where('status', 'rejected')->count() }}</h5>
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

        {{-- Card Jam dan Tanggal --}}
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

  {{-- Card untuk menampilkan semua tiket assigned ke officer dengan pivot.status != proses_qa --}}
  <div class="row mt-4">
    <div class="col-lg-6 mb-lg-0 mb-4 d-flex">
      <div class="card h-100 w-100">
        <div class="card-body p-3 d-flex flex-column" style="height:100%;">
          <h6 class="text-uppercase font-weight-bold mb-3">Butuh Proses Saya</h6>
          <!-- batasi tinggi tabel agar tidak melebihi viewport dan buat scroll internal -->
          <div class="need-process-table-wrap">
            <table class="table table-hover table-sm need-process-table">
              <colgroup>
                <col class="col-no">
                <col class="col-ticket">
                <col class="col-status">
                <col class="col-title">
                <col class="col-action">
              </colgroup>
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
                  $tickets = \App\Models\Ticket::whereHas('officers', function($q){ $q->where('ticket_officer.officer_id', auth()->id())->where('ticket_officer.status', '!=', 'proses_qa'); })
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
                      <a href="{{ url('officer/tindak-lanjut?ticket_id=' . $ticket->id) }}" class="btn btn-sm btn-warning">
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

    {{-- Card untuk menampilkan grafik jumlah tiket --}}
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
            <button type="button" id="filter-button" class="btn btn-primary btn-sm mt-3">Terapkan</button>
          </form>
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
  document.addEventListener('DOMContentLoaded', function () {
    const ctx = document.getElementById('ticket-chart').getContext('2d');
    let chart;

    // Ambil base URL Laravel (otomatis menyesuaikan subfolder/public)
    var baseUrl = "{{ url('/') }}";

    function fetchChartData(startDate, endDate) {
      var url = baseUrl + '/admin/tickets/chart-data?role=officer&start_date=' + encodeURIComponent(startDate) + '&end_date=' + encodeURIComponent(endDate);
      fetch(url)
        .then(response => {
          if (!response.ok) throw new Error('Network response was not ok');
          return response.json();
        })
        .then(data => {
          if (chart) chart.destroy();

          // create gradient similar to Sales Overview
          const gradient = ctx.createLinearGradient(0, 0, 0, 200);
          gradient.addColorStop(0, 'rgba(255, 159, 64, 0.24)');
          gradient.addColorStop(1, 'rgba(255, 159, 64, 0.00)');

          chart = new Chart(ctx, {
            type: 'line',
            data: {
              labels: data.labels,
              datasets: [{
                label: 'Jumlah Tiket',
                data: data.values,
                tension: 0.4,
                borderColor: 'rgba(255,159,64,1)',
                backgroundColor: gradient,
                fill: true,
                pointRadius: 0,
                pointHoverRadius: 6,
                borderWidth: 2,
              }]
            },
            options: {
              responsive: true,
              maintainAspectRatio: false,
              plugins: {
                legend: { display: false }
              },
              interaction: {
                mode: 'index',
                intersect: false,
              },
              scales: {
                x: {
                  grid: { display: false },
                  ticks: { maxRotation: 0, autoSkip: true }
                },
                y: {
                  grid: { drawBorder: false },
                  beginAtZero: true,
                  ticks: { stepSize: 1 }
                }
              }
            }
          });
        })
        .catch(function(err){
          if (chart) chart.destroy();
          ctx.clearRect(0, 0, ctx.canvas.width, ctx.canvas.height);
        });
    }

    document.getElementById('filter-button').addEventListener('click', function () {
      const startDate = document.getElementById('start_date').value;
      const endDate = document.getElementById('end_date').value;
      fetchChartData(startDate, endDate);
    });

    // Fetch initial data for the last 30 days
    const today = new Date().toISOString().split('T')[0];
    const lastMonth = new Date();
    lastMonth.setDate(lastMonth.getDate() - 30);
    const defaultStartDate = lastMonth.toISOString().split('T')[0];
    fetchChartData(defaultStartDate, today);
  });

  // Script untuk menampilkan waktu dan tanggal dalam format Indonesia
  document.addEventListener('DOMContentLoaded', function () {
    function updateTime() {
      const now = new Date();
      const optionsDate = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
      const optionsTime = { hour: '2-digit', minute: '2-digit', second: '2-digit' };

      const time = now.toLocaleTimeString('id-ID', optionsTime);
      const date = now.toLocaleDateString('id-ID', optionsDate);

      document.getElementById('current-time').textContent = time;
      document.getElementById('current-date').textContent = date;
    }

    updateTime();
    setInterval(updateTime, 1000); // Update setiap detik
  });
</script>
@endsection
