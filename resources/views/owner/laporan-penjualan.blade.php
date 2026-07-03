<x-layouts.admin title="Laporan Penjualan" subtitle="Rincian data penjualan dan pendapatan usaha">

    {{-- Filter + Aksi --}}
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
                          d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
                Ekspor PDF
            </a>
        </div>
    </div>

    {{-- Ringkasan --}}
    <div class="bg-emerald-600 rounded-2xl px-8 py-7 mb-6 text-white">
        <p class="text-xs font-semibold uppercase tracking-widest text-emerald-200 mb-2">Total Pendapatan</p>
        <p class="text-4xl font-bold mb-1">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</p>
        <p class="text-sm text-emerald-200">Periode: {{ $start->translatedFormat('F Y') }}</p>

        <div class="mt-6 grid grid-cols-2 gap-3 sm:gap-4">
            <div class="bg-emerald-700/50 rounded-xl px-4 py-3">
                <p class="text-xs text-emerald-300 uppercase tracking-wider mb-1">Total Transaksi</p>
                <p class="text-2xl font-bold">{{ number_format($totalTransaksi, 0, ',', '.') }}</p>
                <p class="text-xs text-emerald-300 mt-0.5">transaksi</p>
            </div>
            <div class="bg-emerald-700/50 rounded-xl px-4 py-3">
                <p class="text-xs text-emerald-300 uppercase tracking-wider mb-1">Rata-rata per Hari</p>
                <p class="text-2xl font-bold">Rp {{ number_format($rataPerHari, 0, ',', '.') }}</p>
                <p class="text-xs text-emerald-300 mt-0.5">per hari</p>
            </div>
        </div>
    </div>

    {{-- Tabel Rincian Transaksi --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h3 class="text-sm font-bold text-slate-900">Rincian Transaksi</h3>
                <p class="text-xs text-slate-400 mt-0.5">{{ $start->translatedFormat('F Y') }}</p>
            </div>
            <span class="text-xs text-slate-400">{{ $totalTransaksi }} transaksi ditemukan</span>
        </div>

        <div class="overflow-x-auto">
        <table class="w-full min-w-[600px]">
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
                        <td class="px-6 py-4 text-sm text-slate-700 whitespace-nowrap">{{ $t->tanggal->format('d M Y') }}</td>
                        <td class="px-6 py-4 text-sm text-slate-600">{{ $t->user->name }}</td>
                        <td class="px-6 py-4 text-sm text-slate-500">{{ $t->keterangan ?? '—' }}</td>
                        <td class="px-6 py-4 text-right text-sm font-semibold tabular-nums text-slate-900">
                            Rp {{ number_format($t->total, 0, ',', '.') }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-12 text-center text-sm text-slate-400">
                            Belum ada transaksi pada periode ini.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        </div>

        @if ($transaksis->hasPages())
            <div class="px-6 py-4 border-t border-slate-100">{{ $transaksis->links() }}</div>
        @endif
    </div>

</x-layouts.admin>
