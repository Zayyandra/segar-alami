<x-layouts.admin title="Dashboard" subtitle="Dashboard Sistem Penjualan dan Persediaan UMKM Segar Alami">

    {{-- Welcome Banner --}}
    <div class="bg-gradient-to-r from-emerald-50 via-emerald-50 to-teal-50 border border-emerald-100
                rounded-2xl px-8 py-5 mb-6 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-full bg-emerald-500 flex items-center justify-center shrink-0 shadow-md shadow-emerald-200">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                </svg>
            </div>
            <div>
                <h2 class="text-lg font-bold text-slate-900">Selamat datang, {{ auth()->user()->name }}</h2>
                <p class="text-sm text-slate-600">Berikut adalah ringkasan operasional Anda hari ini.</p>
            </div>
        </div>
        <div class="text-right shrink-0">
            <p class="text-xs font-bold text-emerald-700 uppercase tracking-wider">Operational Hub</p>
            <p class="text-sm text-slate-700">{{ now()->translatedFormat('l, d F Y') }}</p>
        </div>
    </div>

    {{-- Stat Cards — card pertama hijau gelap sesuai Figma --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">

        {{-- Card 1: Total Penjualan — hijau gelap seperti Figma --}}
        <div class="bg-emerald-700 rounded-2xl shadow-md shadow-emerald-200 px-5 py-5">
            <p class="text-xs text-emerald-300 uppercase tracking-wider mb-2">Total Penjualan Bulan Ini</p>
            <p class="text-2xl font-bold text-white leading-tight">
                Rp {{ number_format($totalBulanIni, 0, ',', '.') }}
            </p>
            @if ($persenChange != 0)
                <p class="text-xs font-medium {{ $persenChange >= 0 ? 'text-emerald-300' : 'text-red-300' }} mt-2">
                    {{ $persenChange >= 0 ? '↗' : '↘' }} {{ number_format(abs($persenChange), 1) }}% vs bln lalu
                </p>
            @else
                <p class="text-xs text-emerald-400 mt-2">Belum ada data perbandingan</p>
            @endif
            <p class="text-xs text-emerald-400 mt-1">Last updated: {{ now()->format('H:i') }}</p>
        </div>

        {{-- Card 2: Produk Terjual --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm px-5 py-5">
            <p class="text-xs text-slate-500 uppercase tracking-wider mb-2">Jumlah Produk Terjual</p>
            <p class="text-2xl font-bold text-slate-900 leading-tight">
                {{ number_format($jumlahTerjual, 0, ',', '.') }}
                <span class="text-sm font-medium text-slate-500">Unit</span>
            </p>
            <p class="text-xs font-medium text-emerald-600 mt-2">Bulan ini</p>
            <p class="text-xs text-slate-400 mt-1">Last updated: {{ now()->format('H:i') }}</p>
        </div>

        {{-- Card 3: Bahan Baku --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm px-5 py-5">
            <p class="text-xs text-slate-500 uppercase tracking-wider mb-2">Bahan Baku Aktif</p>
            <p class="text-2xl font-bold text-slate-900 leading-tight">
                {{ $totalBahanBaku }} <span class="text-sm font-medium text-slate-500">Jenis</span>
            </p>
            <p class="text-xs font-medium text-amber-600 mt-2">⚠ Tracking stok dipause</p>
            <p class="text-xs text-slate-400 mt-1">Last updated: {{ now()->format('H:i') }}</p>
        </div>

        {{-- Card 4: Kategori --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm px-5 py-5">
            <p class="text-xs text-slate-500 uppercase tracking-wider mb-2">Kategori Produk</p>
            <p class="text-2xl font-bold text-slate-900 leading-tight">
                {{ $totalKategori }} <span class="text-sm font-medium text-slate-500">Kategori</span>
            </p>
            <p class="text-xs font-medium text-emerald-600 mt-2">✓ Aktif</p>
            <p class="text-xs text-slate-400 mt-1">Last updated: {{ now()->format('H:i') }}</p>
        </div>
    </div>

    {{-- Charts --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

        <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200 shadow-sm px-6 py-5">
            <h3 class="text-base font-bold text-slate-900">Volume Penjualan Produk</h3>
            <p class="text-xs text-slate-500 mb-5">Performa per kategori produk bulan ini</p>
            @if ($volumePerKategori->isEmpty())
                <div class="h-64 flex items-center justify-center text-sm text-slate-400">
                    Belum ada penjualan bulan ini.
                </div>
            @else
                <div class="h-64"><canvas id="chartVolume"></canvas></div>
            @endif
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm px-6 py-5">
            <h3 class="text-base font-bold text-slate-900">Distribusi Penjualan</h3>
            <p class="text-xs text-slate-500 mb-5">Per kategori produk</p>
            @if ($distribusi->isEmpty())
                <div class="h-48 flex items-center justify-center text-sm text-slate-400">Belum ada data.</div>
            @else
                <div class="h-44 mb-4 flex items-center justify-center">
                    <canvas id="chartDistribusi"></canvas>
                </div>
                <div class="space-y-2">
                    @foreach ($distribusi as $i => $item)
                        <div class="flex items-center justify-between text-sm">
                            <div class="flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full"
                                      style="background-color: {{ ['#10b981','#34d399','#6ee7b7','#a7f3d0','#d1fae5'][$i % 5] }}"></span>
                                <span class="text-slate-700">{{ $item['kategori'] }}</span>
                            </div>
                            <span class="font-medium text-slate-900">{{ $item['persen'] }}%</span>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    @if ($volumePerKategori->isNotEmpty() || $distribusi->isNotEmpty())
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>
        <script>
            const colors = ['#10b981', '#34d399', '#6ee7b7', '#a7f3d0', '#d1fae5'];
            @if ($volumePerKategori->isNotEmpty())
            new Chart(document.getElementById('chartVolume'), {
                type: 'bar',
                data: {
                    labels: @json($volumePerKategori->pluck('kategori')),
                    datasets: [{ data: @json($volumePerKategori->pluck('total')), backgroundColor: colors, borderRadius: 8, maxBarThickness: 60 }]
                },
                options: {
                    responsive: true, maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: { beginAtZero: true, ticks: { callback: v => 'Rp ' + (v >= 1000000 ? (v/1000000).toFixed(1)+'jt' : (v/1000).toFixed(0)+'rb') }, grid: { color: '#f1f5f9' } },
                        x: { grid: { display: false } }
                    }
                }
            });
            @endif
            @if ($distribusi->isNotEmpty())
            new Chart(document.getElementById('chartDistribusi'), {
                type: 'doughnut',
                data: {
                    labels: @json($distribusi->pluck('kategori')),
                    datasets: [{ data: @json($distribusi->pluck('persen')), backgroundColor: colors, borderWidth: 0 }]
                },
                options: { responsive: true, maintainAspectRatio: false, cutout: '70%', plugins: { legend: { display: false } } }
            });
            @endif
        </script>
    @endif

</x-layouts.admin>
