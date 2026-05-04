<x-layouts.admin title="Laporan Persediaan" subtitle="Pantau ketersediaan stok bahan baku secara real-time.">

    {{-- Stat Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">

        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm px-5 py-5">
            <div class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center mb-3">
                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                </svg>
            </div>
            <p class="text-xs text-slate-500 uppercase tracking-wider">Total Jenis</p>
            <p class="text-3xl font-bold text-slate-900 mt-1">{{ $totalJenis }}</p>
            <p class="text-xs text-slate-400 mt-1">Kategori Bahan Baku</p>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm px-5 py-5">
            <div class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center mb-3">
                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <p class="text-xs text-slate-500 uppercase tracking-wider">Aman</p>
            <p class="text-3xl font-bold text-slate-900 mt-1">{{ $jumlahAman }}</p>
            <p class="text-xs text-emerald-600 mt-1">Persediaan Optimal</p>
        </div>

        <div class="bg-red-50 rounded-2xl border border-red-100 shadow-sm px-5 py-5">
            <div class="w-10 h-10 rounded-xl bg-red-100 flex items-center justify-center mb-3">
                <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>
            <p class="text-xs text-red-500 uppercase tracking-wider">Kritis</p>
            <p class="text-3xl font-bold text-red-600 mt-1">{{ $jumlahKritis }}</p>
            <p class="text-xs text-red-500 mt-1">Butuh Re-stock Segera</p>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm px-5 py-5">
            <div class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center mb-3">
                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <p class="text-xs text-slate-500 uppercase tracking-wider">Total Nilai</p>
            <p class="text-2xl font-bold text-slate-900 mt-1">Rp {{ number_format($totalNilai, 0, ',', '.') }}</p>
            <p class="text-xs text-slate-400 mt-1">Valuasi Stok Gudang</p>
        </div>
    </div>

    {{-- Tabel Rincian --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
            <h3 class="text-sm font-bold text-slate-900">Rincian Persediaan</h3>
            <div class="flex flex-wrap items-center gap-3">
                <form method="GET" action="{{ route('app.laporan.persediaan') }}" class="flex gap-2">
                    <div class="relative">
                        <svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-4.35-4.35M11 19a8 8 0 100-16 8 8 0 000 16z" />
                        </svg>
                        <input type="search" name="q" value="{{ request('q') }}"
                            placeholder="Cari bahan baku..."
                            class="pl-10 pr-3 py-2 rounded-lg border border-slate-200 text-sm
                                   focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 focus:outline-none">
                    </div>
                    <select name="status" onchange="this.form.submit()"
                        class="min-w-[150px] px-3 pr-8 py-2 rounded-lg border border-slate-200 text-sm bg-white
                               focus:border-emerald-500 focus:outline-none">
                        <option value="">Semua Status</option>
                        <option value="aman" @selected(request('status') === 'aman')>Aman</option>
                        <option value="kritis" @selected(request('status') === 'kritis')>Kritis</option>
                    </select>
                </form>
                <div class="flex items-center gap-2 text-xs text-slate-500">
                    <span class="inline-flex items-center gap-1">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span> Aman
                    </span>
                    <span class="inline-flex items-center gap-1">
                        <span class="w-2 h-2 rounded-full bg-red-500"></span> Kritis
                    </span>
                </div>
                <a href="{{ route('app.laporan.persediaan.pdf') }}"
                   class="px-4 py-2 rounded-lg border border-slate-200 bg-white text-slate-700
                          text-sm font-medium hover:bg-slate-50 transition flex items-center gap-2 whitespace-nowrap">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    Export PDF
                </a>
            </div>
        </div>

        <table class="w-full">
            <thead class="bg-slate-50">
                <tr class="text-left text-xs uppercase tracking-wide text-slate-500">
                    <th class="px-6 py-3 font-medium">Nama Bahan Baku</th>
                    <th class="px-6 py-3 font-medium">Stok Saat Ini</th>
                    <th class="px-6 py-3 font-medium">Stok Minimum</th>
                    <th class="px-6 py-3 font-medium">Status</th>
                    <th class="px-6 py-3 font-medium text-right">Nilai Stok</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($bahanBakus as $item)
                    @php $status = $item->status_stok; @endphp
                    <tr class="hover:bg-slate-50/50">
                        <td class="px-6 py-4">
                            <p class="text-sm font-medium text-slate-900">{{ $item->nama }}</p>
                            <p class="text-xs text-slate-500">{{ ucfirst($item->kategori_bb) }}</p>
                        </td>
                        <td class="px-6 py-4 text-sm {{ $status === 'kritis' ? 'text-red-600 font-semibold' : 'text-slate-700' }}">
                            {{ number_format($item->stok_saat_ini, 2, ',', '.') }} {{ $item->satuan }}
                        </td>
                        <td class="px-6 py-4 text-sm text-slate-600">
                            {{ number_format($item->stok_minimum, 2, ',', '.') }} {{ $item->satuan }}
                        </td>
                        <td class="px-6 py-4">
                            @if ($status === 'aman')
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700">Aman</span>
                            @elseif ($status === 'kritis')
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-red-50 text-red-700">Kritis</span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-slate-100 text-slate-500">—</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right text-sm font-medium tabular-nums text-slate-900">
                            Rp {{ number_format($item->nilai_stok, 0, ',', '.') }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-sm text-slate-400">
                            Belum ada data bahan baku.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if ($bahanBakus->hasPages())
            <div class="px-6 py-4 border-t border-slate-100">{{ $bahanBakus->links() }}</div>
        @endif

        <div class="px-6 py-3 border-t border-slate-100 flex items-center justify-between">
            <p class="text-xs text-slate-400">
                Data diperbarui setiap kali stok diupdate. Terakhir: {{ now()->translatedFormat('d M Y, H:i') }} WIB
            </p>
        </div>
    </div>

</x-layouts.admin>
