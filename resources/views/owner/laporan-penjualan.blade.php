<x-layouts.admin title="Laporan Penjualan" subtitle="Ringkasan data penjualan dan pendapatan usaha">

    {{-- Header + filter --}}
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
        <div></div>
        <div class="flex items-center gap-3">
            <form method="GET" action="{{ route('app.laporan.penjualan') }}" class="flex items-center gap-2">
                <input type="month" name="bulan" value="{{ $bulan }}" onchange="this.form.submit()"
                    class="px-3 py-2 rounded-lg border border-slate-200 text-sm bg-white
                      focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 focus:outline-none">
            </form>
            <a href="{{ route('app.laporan.penjualan.pdf', ['bulan' => $bulan]) }}"
                class="px-4 py-2 rounded-lg border border-slate-200 bg-white text-slate-700
              text-sm font-medium hover:bg-slate-50 transition flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                </svg>
                Export PDF
            </a>
            <a href="{{ route('app.penjualan.create') }}"
                class="px-4 py-2 rounded-lg bg-emerald-500 hover:bg-emerald-600 text-white text-sm font-medium transition whitespace-nowrap">
                + Catat Penjualan
            </a>
        </div>
    </div>

    {{-- Hero card --}}
    <div class="bg-emerald-600 rounded-2xl px-8 py-7 mb-6 text-white">
        <p class="text-xs font-semibold uppercase tracking-widest text-emerald-200 mb-2">Total Pendapatan Bulan Ini</p>
        <p class="text-4xl font-bold mb-1">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</p>
        <p class="text-sm text-emerald-200">Periode: {{ $start->translatedFormat('F Y') }}</p>

        <div class="mt-6 grid grid-cols-2 gap-4">
            <div class="bg-emerald-700/50 rounded-xl px-4 py-3">
                <p class="text-xs text-emerald-300 uppercase tracking-wider mb-1">Total Transaksi</p>
                <p class="text-2xl font-bold">{{ number_format($totalTransaksi, 0, ',', '.') }}</p>
                <p class="text-xs text-emerald-300 mt-0.5">transaksi</p>
            </div>
            <div class="bg-emerald-700/50 rounded-xl px-4 py-3">
                <p class="text-xs text-emerald-300 uppercase tracking-wider mb-1">Rata-rata / Hari</p>
                <p class="text-2xl font-bold">Rp {{ number_format($rataPerHari, 0, ',', '.') }}</p>
                <p class="text-xs text-emerald-300 mt-0.5">per hari</p>
            </div>
        </div>
    </div>

    {{-- Charts + Produk Terlaris --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 mb-6">

        {{-- Tren Harian --}}
        <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200 shadow-sm px-6 py-5">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-base font-bold text-slate-900">Tren Penjualan Harian</h3>
                    <p class="text-xs text-slate-500">{{ $start->translatedFormat('F Y') }}</p>
                </div>
            </div>
            <div class="h-56">
                <canvas id="chartTren"></canvas>
            </div>
        </div>

        {{-- Produk Terlaris --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm px-6 py-5">
            <h3 class="text-base font-bold text-slate-900 mb-1">Produk Terlaris</h3>
            <p class="text-xs text-slate-500 mb-4">Distribusi unit penjualan</p>

            @if ($produkTerlaris->isEmpty())
                <p class="text-sm text-slate-400 text-center py-8">Belum ada data.</p>
            @else
                <div class="space-y-3">
                    @foreach ($produkTerlaris->take(5) as $item)
                        @php
                            $persen = $totalUnit > 0 ? round(($item->total_unit / $totalUnit) * 100) : 0;
                        @endphp
                        <div>
                            <div class="flex items-center justify-between text-sm mb-1">
                                <span class="text-slate-700 truncate max-w-[160px]">{{ $item->nama }}</span>
                                <span class="font-semibold text-slate-900 ml-2">{{ $persen }}%</span>
                            </div>
                            <div class="w-full bg-slate-100 rounded-full h-2">
                                <div class="bg-emerald-500 h-2 rounded-full" style="width: {{ $persen }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    {{-- Tabel Transaksi --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <h3 class="text-sm font-bold text-slate-900">Rincian Transaksi</h3>
        </div>
        <table class="w-full">
            <thead class="bg-slate-50">
                <tr class="text-left text-xs uppercase tracking-wide text-slate-500">
                    <th class="px-6 py-3 font-medium">Tanggal</th>
                    <th class="px-6 py-3 font-medium">Dicatat Oleh</th>
                    <th class="px-6 py-3 font-medium">Keterangan</th>
                    <th class="px-6 py-3 font-medium text-right">Total</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($transaksis as $t)
                    <tr class="hover:bg-slate-50/50">
                        <td class="px-6 py-4 text-sm text-slate-700">{{ $t->tanggal->format('d M Y') }}</td>
                        <td class="px-6 py-4 text-sm text-slate-600">{{ $t->user->name }}</td>
                        <td class="px-6 py-4 text-sm text-slate-500">{{ $t->keterangan ?? '—' }}</td>
                        <td class="px-6 py-4 text-right text-sm font-semibold tabular-nums text-slate-900">
                            Rp {{ number_format($t->total, 0, ',', '.') }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-12 text-center text-sm text-slate-400">
                            Belum ada transaksi bulan ini.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        @if ($transaksis->hasPages())
            <div class="px-6 py-4 border-t border-slate-100">{{ $transaksis->links() }}</div>
        @endif
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>
    <script>
        new Chart(document.getElementById('chartTren'), {
            type: 'bar',
            data: {
                labels: @json($days),
                datasets: [{
                    data: @json($totals),
                    backgroundColor: '#10b981',
                    borderRadius: 6,
                    maxBarThickness: 20
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: v => v >= 1000000 ?
                                'Rp ' + (v / 1000000).toFixed(1) + 'jt' :
                                'Rp ' + (v / 1000).toFixed(0) + 'rb'
                        },
                        grid: {
                            color: '#f1f5f9'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    </script>

</x-layouts.admin>
