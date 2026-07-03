<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Laporan Persediaan {{ now()->format('d-m-Y') }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 11px;
            color: #0f172a;
            background: #fff;
        }

        .header {
            padding: 20px 30px;
            border-bottom: 2px solid #10b981;
            margin-bottom: 20px;
        }

        .header-table {
            width: 100%;
            border-collapse: collapse;
        }

        .brand {
            font-size: 20px;
            font-weight: bold;
            color: #059669;
        }

        .brand-sub {
            font-size: 10px;
            color: #475569;
            margin-top: 2px;
        }

        .doc-title {
            text-align: right;
        }

        .doc-title h1 {
            font-size: 16px;
            font-weight: bold;
            color: #0f172a;
        }

        .doc-title p {
            font-size: 10px;
            color: #475569;
            margin-top: 3px;
        }

        /* Stat cards pakai table — DomPDF tidak support flexbox */
        .stat-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 8px;
            margin: 0 0 12px 0;
            padding: 0 22px;
        }

        .stat-card {
            padding: 12px 14px;
            border-radius: 6px;
            border: 1px solid #cbd5e1;
            vertical-align: top;
            width: 25%;
        }

        .stat-card-red {
            padding: 12px 14px;
            border-radius: 6px;
            border: 1px solid #fca5a5;
            background: #fee2e2;
            vertical-align: top;
            width: 25%;
        }

        .stat-label {
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #475569;
            margin-bottom: 4px;
            font-weight: bold;
        }

        .stat-label-red {
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #991b1b;
            margin-bottom: 4px;
            font-weight: bold;
        }

        .stat-value {
            font-size: 18px;
            font-weight: bold;
            color: #0f172a;
        }

        .stat-value-green {
            font-size: 18px;
            font-weight: bold;
            color: #059669;
        }

        .stat-value-red {
            font-size: 18px;
            font-weight: bold;
            color: #dc2626;
        }

        .stat-value-sm {
            font-size: 13px;
            font-weight: bold;
            color: #0f172a;
        }

        .stat-sub {
            font-size: 9px;
            color: #64748b;
            margin-top: 3px;
        }

        .stat-sub-red {
            font-size: 9px;
            color: #dc2626;
            margin-top: 3px;
        }

        .section {
            margin: 0 30px 20px;
        }

        .section-title {
            font-size: 12px;
            font-weight: bold;
            color: #0f172a;
            margin-bottom: 10px;
            padding-bottom: 6px;
            border-bottom: 1px solid #cbd5e1;
        }

        table.data-table {
            width: 100%;
            border-collapse: collapse;
        }

        table.data-table thead tr {
            background: #f1f5f9;
        }

        table.data-table th {
            padding: 8px 10px;
            text-align: left;
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #334155;
            font-weight: bold;
        }

        table.data-table td {
            padding: 8px 10px;
            font-size: 10px;
            color: #1e293b;
            border-bottom: 1px solid #e2e8f0;
        }

        table.data-table td.right {
            text-align: right;
        }

        table.data-table tr:last-child td {
            border-bottom: none;
        }

        .kritis {
            color: #dc2626;
            font-weight: bold;
        }

        .badge-aman {
            background: #d1fae5;
            color: #065f46;
            padding: 2px 8px;
            border-radius: 20px;
            font-size: 9px;
            font-weight: bold;
        }

        .badge-kritis {
            background: #fee2e2;
            color: #991b1b;
            padding: 2px 8px;
            border-radius: 20px;
            font-size: 9px;
            font-weight: bold;
        }

        .badge-unknown {
            background: #f1f5f9;
            color: #475569;
            padding: 2px 8px;
            border-radius: 20px;
            font-size: 9px;
        }

        .footer {
            margin: 20px 30px 0;
            padding-top: 12px;
            border-top: 1px solid #cbd5e1;
        }

        .footer-table {
            width: 100%;
            border-collapse: collapse;
        }

        .footer-left {
            font-size: 9px;
            color: #64748b;
        }

        .footer-right {
            font-size: 9px;
            color: #64748b;
            text-align: right;
        }
    </style>
</head>

<body>

    <div class="header">
        <table class="header-table">
            <tr>
                <td>
                    <div class="brand">Segar Alami</div>
                    <div class="brand-sub">Susu Kedelai dan Kembang Tahu</div>
                </td>
                <td class="doc-title">
                    <h1>Laporan Persediaan Bahan Baku</h1>
                    <p>Per tanggal: {{ now()->translatedFormat('d F Y') }}</p>
                    <p>Dicetak: {{ now()->translatedFormat('d F Y, H:i') }} WIB</p>
                </td>
            </tr>
        </table>
    </div>

    {{-- Stat cards pakai <table> karena DomPDF tidak support flexbox --}}
    <table class="stat-table">
        <tr>
            <td class="stat-card">
                <div class="stat-label">Total Jenis</div>
                <div class="stat-value">{{ $totalJenis }}</div>
                <div class="stat-sub">Bahan Baku</div>
            </td>
            <td class="stat-card">
                <div class="stat-label">Aman</div>
                <div class="stat-value-green">{{ $jumlahAman }}</div>
                <div class="stat-sub">Persediaan optimal</div>
            </td>
            <td class="stat-card-red">
                <div class="stat-label-red">Kritis</div>
                <div class="stat-value-red">{{ $jumlahKritis }}</div>
                <div class="stat-sub-red">Butuh restock</div>
            </td>
            <td class="stat-card">
                <div class="stat-label">Total Nilai Stok</div>
                <div class="stat-value-sm">Rp {{ number_format($totalNilai, 0, ',', '.') }}</div>
                <div class="stat-sub">Valuasi gudang</div>
            </td>
        </tr>
    </table>

    <div class="section">
        <div class="section-title">Rincian Persediaan Bahan Baku</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nama Bahan Baku</th>
                    <th>Kategori</th>
                    <th>Satuan</th>
                    <th class="right">Stok Saat Ini</th>
                    <th class="right">Stok Minimum</th>
                    <th>Status</th>
                    <th class="right">Nilai Stok</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($bahanBakus as $i => $item)
                    @php
                        $ss = $batch[$item->id]['ss'] ?? null;
                        $statusItem = $ss === null ? 'unknown' : ($item->stok_saat_ini <= $ss ? 'kritis' : 'aman');
                    @endphp
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td>{{ $item->nama }}</td>
                        <td>{{ ucfirst($item->kategori_bb) }}</td>
                        <td>{{ $item->satuan }}</td>
                        <td class="right {{ $statusItem === 'kritis' ? 'kritis' : '' }}">
                            {{ number_format($item->stok_saat_ini, 0, ',', '.') }}
                        </td>
                        <td class="right">
                            {{ $ss !== null ? number_format($ss, 0, ',', '.') : '—' }}
                        </td>
                        <td>
                            @if ($statusItem === 'aman')
                                <span class="badge-aman">Aman</span>
                            @elseif ($statusItem === 'kritis')
                                <span class="badge-kritis">Kritis</span>
                            @else
                                <span class="badge-unknown">—</span>
                            @endif
                        </td>
                        <td class="right">Rp {{ number_format($item->nilai_stok, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="footer">
        <table class="footer-table">
            <tr>
                <td class="footer-left">Segar Alami &middot; Sistem Informasi UMKM</td>
                <td class="footer-right">Laporan ini digenerate otomatis oleh sistem</td>
            </tr>
        </table>
    </div>

</body>

</html>
