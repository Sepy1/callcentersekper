<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Riwayat Tiket {{ $ticket->nomor_tiket }}</title>
    <style>
        body{font-family:DejaVu Sans,sans-serif;color:#27324a;font-size:11px;line-height:1.5}
        h1{font-size:20px;margin:0 0 4px}.muted{color:#6b7280}.summary{width:100%;margin:20px 0;border-collapse:collapse}.summary td{width:50%;padding:7px 10px;border:1px solid #dfe3eb}.label{display:block;color:#6b7280;font-size:9px;text-transform:uppercase}.history{border-left:2px solid #7656d8;margin-left:7px;padding-left:18px}.item{position:relative;margin:0 0 14px;padding:10px 12px;border:1px solid #e3e6ed;border-radius:6px}.item:before{content:'';position:absolute;left:-25px;top:14px;width:10px;height:10px;border-radius:50%;background:#7656d8}.item-title{font-weight:bold;font-size:12px}.time{float:right;color:#6b7280;font-size:9px}.detail{margin-top:5px;color:#4b5563}.footer{margin-top:24px;color:#6b7280;font-size:9px;text-align:right}
    </style>
</head>
<body>
    <h1>Riwayat Tiket</h1>
    <div class="muted">{{ $ticket->nomor_tiket }}</div>
    <table class="summary">
        <tr><td><span class="label">Pelapor</span>{{ $ticket->nama_pelapor ?: '-' }}</td><td><span class="label">Kategori</span>{{ $ticket->kategori_nama }}</td></tr>
        <tr><td><span class="label">Judul</span>{{ $ticket->judul ?: '-' }}</td><td><span class="label">Status</span>{{ ['open' => 'Baru', 'in_progress' => 'Sedang Diproses', 'resolved' => 'Selesai oleh QA', 'closed' => 'Ditutup', 'rejected' => 'Ditolak'][$ticket->status] ?? ucfirst(str_replace('_', ' ', $ticket->status)) }}</td></tr>
    </table>
    <div class="history">
        @forelse($history as $item)
            <div class="item">
                <span class="time">{{ $item->created_at->format('d/m/Y H:i') }} WIB</span>
                <div class="item-title">{{ $item->action_label }}</div>
                <div class="detail">{{ $item->detail_indonesia }}</div>
            </div>
        @empty
            <div class="muted">Belum ada riwayat aktivitas.</div>
        @endforelse
    </div>
    <div class="footer">Diunduh pada {{ now()->format('d/m/Y H:i') }} WIB</div>
</body>
</html>
