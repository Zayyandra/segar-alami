<x-layouts.admin title="Penjualan" subtitle="Manajemen transaksi penjualan Segar Alami.">

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm px-6 py-5 flex items-center gap-4">
            <div class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                </svg>
            </div>
            <div>
                <p class="text-xs text-slate-500 uppercase tracking-wider mb-1">Total Omzet Hari Ini</p>
                <p class="text-2xl font-bold text-slate-900">Rp {{ number_format($totalOmzetHariIni, 0, ',', '.') }}</p>
                <p class="text-xs text-emerald-600 font-medium mt-1">{{ $transaksiHariIni ?? 0 }} transaksi hari ini</p>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm px-6 py-5 flex items-center gap-4">
            <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                </svg>
            </div>
            <div>
                <p class="text-xs text-slate-500 uppercase tracking-wider mb-1">Produk Terjual Hari Ini</p>
                <p class="text-2xl font-bold text-slate-900">{{ number_format($totalProdukTerjual ?? 0, 0, ',', '.') }} <span class="text-sm font-normal text-slate-400">Items</span></p>
                <p class="text-xs text-blue-600 font-medium mt-1">
                    {{ ($totalProdukTerjual ?? 0) > 0 ? 'Bulan ini' : 'Belum ada penjualan' }}
                </p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h3 class="text-sm font-bold text-slate-900">Log Transaksi</h3>
                <p class="text-xs text-slate-400 mt-0.5">{{ now()->translatedFormat('d F Y') }}</p>
            </div>
            <a href="{{ route('app.penjualan.create') }}"
               class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg bg-emerald-500 hover:bg-emerald-600 text-white text-sm font-medium transition">
                + Tambah Data Penjualan
            </a>
        </div>

        <div class="px-6 py-3 border-b border-slate-100 flex gap-3">
            <form method="GET" action="{{ route('app.penjualan.index') }}" class="flex flex-1 gap-3">
                <div class="relative flex-1">
                    <svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M11 19a8 8 0 100-16 8 8 0 000 16z"/>
                    </svg>
                    <input type="search" name="q" value="{{ request('q') }}" placeholder="Cari transaksi..."
                           class="w-full pl-10 px-3 py-2 rounded-lg border border-slate-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 focus:outline-none">
                </div>
                <input type="date" name="tanggal" value="{{ request('tanggal') }}" onchange="this.form.submit()"
                       class="px-3 py-2 rounded-lg border border-slate-200 text-sm bg-white focus:border-emerald-500 focus:outline-none">
                @if (request()->anyFilled(['q', 'tanggal']))
                    <a href="{{ route('app.penjualan.index') }}" class="px-4 py-2 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-100 transition">Reset</a>
                @endif
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50">
                    <tr class="text-left text-xs uppercase tracking-wide text-slate-500">
                        <th class="px-6 py-3 font-medium">Tanggal</th>
                        <th class="px-6 py-3 font-medium">Dicatat Oleh</th>
                        <th class="px-6 py-3 font-medium">Keterangan</th>
                        <th class="px-6 py-3 font-medium text-right">Total</th>
                        <th class="px-6 py-3 font-medium text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($penjualans as $item)
                        <tr class="hover:bg-slate-50/50">
                            <td class="px-6 py-4 text-sm text-slate-700 whitespace-nowrap">{{ $item->tanggal->format('d M Y') }}</td>
                            <td class="px-6 py-4 text-sm text-slate-600">{{ $item->user->name }}</td>
                            <td class="px-6 py-4 text-sm text-slate-500">{{ $item->keterangan ?? '—' }}</td>
                            <td class="px-6 py-4 text-right text-sm font-bold tabular-nums text-slate-900">
                                Rp {{ number_format($item->total, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="inline-flex items-center gap-3">
                                    <a href="{{ route('app.penjualan.show', $item) }}" class="text-sm font-medium text-emerald-600 hover:text-emerald-700">Detail</a>
                                    <form method="POST" action="{{ route('app.penjualan.destroy', $item) }}" class="inline"
                                          onsubmit="return confirm('Hapus transaksi ini?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-sm font-medium text-red-500 hover:text-red-600">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-16 text-center text-sm text-slate-400">
                                Belum ada transaksi.
                                <a href="{{ route('app.penjualan.create') }}" class="text-emerald-600 hover:underline">Catat sekarang.</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($penjualans->hasPages())
            <div class="px-6 py-4 border-t border-slate-100">{{ $penjualans->links() }}</div>
        @endif
    </div>

</x-layouts.admin>
