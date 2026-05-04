<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Laporan Penjualan {{ $start->format('F Y') }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Helvetica', sans-serif; font-size: 11px; color: #1e293b; background: #fff; }

        .header { padding: 20px 30px; border-bottom: 2px solid #10b981; margin-bottom: 20px; }
        .header-top { display: flex; justify-content: space-between; align-items: flex-start; }
        .brand { font-size: 20px; font-weight: bold; color: #10b981; }
        .brand-sub { font-size: 10px; color: #64748b; margin-top: 2px; }
        .doc-title { text-align: right; }
        .doc-title h1 { font-size: 16px; font-weight: bold; color: #1e293b; }
        .doc-title p { font-size: 10px; color: #64748b; margin-top: 3px; }

        .stat-row { display: flex; gap: 12px; margin: 0 30px 20px; }
        .stat-card { flex: 1; padding: 12px 16px; border-radius: 8px; border: 1px solid #e2e8f0; }
        .stat-card.green { background: #10b981; border-color: #10b981; }
        .stat-card.green .stat-label { color: #d1fae5; }
        .stat-card.green .stat-value { color: #fff; }
        .stat-label { font-size: 9px; text-transform: uppercase; letter-spacing: 0.05em; color: #64748b; margin-bottom: 4px; }
        .stat-value { font-size: 16px; font-weight: bold; color: #1e293b; }
        .stat-sub { font-size: 9px; color: #94a3b8; margin-top: 3px; }

        .section { margin: 0 30px 20px; }
        .section-title { font-size: 12px; font-weight: bold; color: #1e293b; margin-bottom: 10px; padding-bottom: 6px; border-bottom: 1px solid #e2e8f0; }

        table { width: 100%; border-collapse: collapse; }
        thead tr { background: #f8fafc; }
        th { padding: 8px 10px; text-align: left; font-size: 9px; text-transform: uppercase; letter-spacing: 0.05em; color: #64748b; font-weight: 600; }
        td { padding: 8px 10px; font-size: 10px; color: #374151; border-bottom: 1px solid #f1f5f9; }
        td.right { text-align: right; }
        td.center { text-align: center; }
        tr:last-child td { border-bottom: none; }

        .total-row td { font-weight: bold; background: #f8fafc; color: #10b981; font-size: 11px; }
        .badge-aman { background: #d1fae5; color: #065f46; padding: 2px 8px; border-radius: 20px; font-size: 9px; }
        .badge-kritis { background: #fee2e2; color: #991b1b; padding: 2px 8px; border-radius: 20px; font-size: 9px; }

        .footer { margin: 20px 30px 0; padding-top: 12px; border-top: 1px solid #e2e8f0; display: flex; justify-content: space-between; font-size: 9px; color: #94a3b8; }
    </style>
</head>
<body>

    <div class="header">
        <div class="header-top">
            <div>
                <div class="brand">Segar Alami</div>
                <div class="brand-sub">Susu Kedelai dan Kembang Tahu</div>
            </div>
            <div class="doc-title">
                <h1>Laporan Penjualan</h1>
                <p>Periode: {{ $start->translatedFormat('F Y') }}</p>
                <p>Dicetak: {{ now()->translatedFormat('d F Y, H:i') }} WIB</p>
            </div>
        </div>
    </div>

    <div class="stat-row">
        <div class="stat-card green">
            <div class="stat-label">Total Pendapatan</div>
            <div class="stat-value">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</div>
            <div class="stat-sub" style="color: #d1fae5;">Periode {{ $start->format('F Y') }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Total Transaksi</div>
            <div class="stat-value">{{ number_format($totalTransaksi, 0, ',', '.') }}</div>
            <div class="stat-sub">transaksi</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Rata-rata / Hari</div>
            <div class="stat-value">Rp {{ number_format($rataPerHari, 0, ',', '.') }}</div>
            <div class="stat-sub">per hari</div>
        </div>
    </div>

    @if ($produkTerlaris->isNotEmpty())
    <div class="section">
        <div class="section-title">Produk Terlaris</div>
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nama Produk</th>
                    <th class="right">Unit Terjual</th>
                    <th class="right">Total Pendapatan</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($produkTerlaris as $i => $item)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $item->nama }}</td>
                    <td class="right">{{ number_format($item->total_unit, 0, ',', '.') }}</td>
                    <td class="right">Rp {{ number_format($item->total_pendapatan, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <div class="section">
        <div class="section-title">Rincian Transaksi</div>
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Tanggal</th>
                    <th>Dicatat Oleh</th>
                    <th>Keterangan</th>
                    <th class="right">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($transaksis as $i => $t)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $t->tanggal->format('d M Y') }}</td>
                    <td>{{ $t->user->name }}</td>
                    <td>{{ $t->keterangan ?? '—' }}</td>
                    <td class="right">Rp {{ number_format($t->total, 0, ',', '.') }}</td>
                </tr>
                @endforeach
                <tr class="total-row">
                    <td colspan="4" style="text-align: right;">Total Keseluruhan</td>
                    <td class="right">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="footer">
        <span>Segar Alami · Sistem Informasi UMKM</span>
        <span>Laporan ini digenerate otomatis oleh sistem</span>
    </div>

</body>
</html>
