<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Laporan Bulanan Call Center</title>
    <style>
    body { font-family: DejaVu Sans, Arial, Helvetica, sans-serif; font-size:12px; color:#000 }
    .header { text-align:left; margin-bottom:20px }
    .title { font-weight:bold; font-size:14px; margin-bottom:6px }
    table { border-collapse: collapse; width:100% }
    table th, table td { border:1px solid #000; padding:6px; text-align:left }
    .no-border { border: none }
    .center { text-align:center }
    /* force signatures to remain on one row when printing */
    .sign-row { margin-top:40px; display:table; width:100%; table-layout:fixed }
    .signature { display:table-cell; width:33%; text-align:center; vertical-align:top }
  </style>
</head>
<body>
  <div class="header">
    <div class="title">PT BPR BKK JAWA TENGAH (Perseroda)</div>
    <div>Laporan Bulanan Call Center</div>
    <div>Bulan : {{ 
      \Carbon\Carbon::parse($startDate)->translatedFormat('F') }} &nbsp;&nbsp; Tahun : {{ \Carbon\Carbon::parse($startDate)->format('Y') }}</div>
  </div>

  <table>
    <thead>
      <tr>
        <th style="width:4%">No</th>
        <th style="width:12%">Tanggal</th>
        <th style="width:22%">Nama Pelapor</th>
        <th style="width:18%">Jenis Aduan</th>
        <th style="width:14%">Unit Terkait</th>
        <th style="width:16%">Status Penyelesaian</th>
        <th style="width:14%">Keterangan</th>
      </tr>
    </thead>
    <tbody>
      @foreach($tickets as $i => $t)
        <tr>
          <td class="center">{{ $i + 1 }}</td>
          <td>{{ optional($t->created_at)->format('Y-m-d') }}</td>
          <td>{{ $t->nama_pelapor }}</td>
          <td>{{ $t->kategori }}</td>
          <td>
            @if(isset($t->officers) && $t->officers->isNotEmpty())
              {{ $t->officers->pluck('name')->join(', ') }}
            @else
              {{ $t->officer }}
            @endif
          </td>
          <td>{{ ucfirst($t->status) }}</td>
          <td>{{ \Illuminate\Support\Str::limit($t->closing_notes ?? $t->judul ?? $t->detail ?? '', 60) }}</td>
        </tr>
      @endforeach
      @if($tickets->isEmpty())
        <tr>
          <td colspan="7" class="center">Tidak ada data</td>
        </tr>
      @endif
    </tbody>
  </table>

  <div style="margin-top:16px">
    <strong>Rekapitulasi Bulanan:</strong>
    <ol>
      <li>Jumlah Pengaduan Diterima : {{ $jumlahDiterima }}</li>
      <li>Jumlah Pengaduan Selesai : {{ $jumlahSelesai }}</li>
      <li>Jumlah Pengaduan Dalam Proses : {{ $jumlahProses }}</li>
      <li>Rata-rata Waktu Penyelesaian : {{ $avgHours }} jam/hari</li>
    </ol>
  </div>

  <div class="sign-row">
    <div class="signature">Dibuat<br><br><br>_________________<br>CSA</div>
    <div class="signature">Diperiksa<br><br><br>_________________<br>Kepala Bidang Sekretaris, Perusahaan & Humas</div>
    <div class="signature">Disetujui<br><br><br>_________________<br>Sekretaris Perusahaan</div>
  </div>

</body>
</html>
