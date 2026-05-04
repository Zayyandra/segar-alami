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

    {{-- Expired Alert --}}
    @if ($expiredAlert->count())
        <div class="rounded-xl border border-red-200 bg-red-50 px-5 py-4 mb-6">
            <p class="text-sm font-semibold text-red-700 mb-2">
                ⚠ {{ $expiredAlert->count() }} bahan baku perlu diperhatikan
            </p>
            <div class="space-y-1.5">
                @foreach ($expiredAlert as $item)
                    <div class="flex items-center justify-between text-xs text-red-700">
                        <span>{{ $item->bahanBaku->nama }}
                            @if($item->nama_supplier)
                                <span class="text-red-400">({{ $item->nama_supplier }})</span>
                            @endif
                        </span>
                        @if(\Carbon\Carbon::parse($item->tanggal_kadaluarsa)->lt(today()))
                            <span class="font-semibold bg-red-100 px-2 py-0.5 rounded">Sudah kadaluarsa</span>
                        @else
                            <span class="text-red-600">Exp: {{ \Carbon\Carbon::parse($item->tanggal_kadaluarsa)->format('d M Y') }}
                                ({{ \Carbon\Carbon::parse($item->tanggal_kadaluarsa)->diffForHumans() }})
                            </span>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Stat Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">

        <div class="bg-emerald-700 rounded-2xl shadow-md shadow-emerald-200 px-5 py-5">
            <p class="text-xs text-emerald-300 uppercase tracking-wider mb-2">Pendapatan Bulan Ini</p>
            <p class="text-2xl font-bold text-white leading-tight">
                Rp {{ number_format($pendapatanBulanIni, 0, ',', '.') }}
            </p>
            <p class="text-xs text-emerald-300 mt-2">{{ $transaksiHariIni }} transaksi hari ini</p>
            <p class="text-xs text-emerald-400 mt-1">Last updated: {{ now()->format('H:i') }}</p>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm px-5 py-5">
            <p class="text-xs text-slate-500 uppercase tracking-wider mb-2">Produk Aktif</p>
            <p class="text-2xl font-bold text-slate-900 leading-tight">
                {{ $totalProdukAktif }} <span class="text-sm font-medium text-slate-500">Produk</span>
            </p>
            <p class="text-xs text-slate-500 mt-2">{{ $totalVarianAktif }} varian tersedia</p>
            <p class="text-xs text-slate-400 mt-1">Last updated: {{ now()->format('H:i') }}</p>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm px-5 py-5">
            <p class="text-xs text-slate-500 uppercase tracking-wider mb-2">Bahan Baku Aktif</p>
            <p class="text-2xl font-bold text-slate-900 leading-tight">
                {{ $totalBahanBaku }} <span class="text-sm font-medium text-slate-500">Jenis</span>
            </p>
            @if ($expiredAlert->count())
                <p class="text-xs font-medium text-red-600 mt-2">⚠ {{ $expiredAlert->count() }} perlu dicek</p>
            @else
                <p class="text-xs font-medium text-emerald-600 mt-2">✓ Semua aman</p>
            @endif
            <p class="text-xs text-slate-400 mt-1">Last updated: {{ now()->format('H:i') }}</p>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm px-5 py-5">
            <p class="text-xs text-slate-500 uppercase tracking-wider mb-2">Kategori Produk</p>
            <p class="text-2xl font-bold text-slate-900 leading-tight">
                {{ $totalKategori }} <span class="text-sm font-medium text-slate-500">Kategori</span>
            </p>
            <p class="text-xs font-medium text-emerald-600 mt-2">✓ Aktif</p>
            <p class="text-xs text-slate-400 mt-1">Last updated: {{ now()->format('H:i') }}</p>
        </div>
    </div>

    {{-- Transaksi Terbaru --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <h3 class="text-sm font-bold text-slate-900">Transaksi Terbaru</h3>
            <a href="{{ route('app.penjualan.index') }}"
               class="text-sm font-medium text-emerald-600 hover:text-emerald-700">Lihat semua →</a>
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
                @forelse ($transaksiTerbaru as $t)
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
                        <td colspan="4" class="px-6 py-10 text-center text-sm text-slate-400">
                            Belum ada transaksi. <a href="{{ route('app.penjualan.create') }}" class="text-emerald-600 hover:underline">Catat sekarang.</a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</x-layouts.admin>
