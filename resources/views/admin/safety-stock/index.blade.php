<x-layouts.admin title="Safety Stock & ROP" subtitle="Perhitungan Safety Stock dan Reorder Point bahan baku Segar Alami.">

    @php
        $totalTracking = $bahanBakus->count();
        $totalAman = $bahanBakus->where('perlu_reorder', false)->where('ss', '!=', null)->count();
        $totalReorder = $bahanBakus->where('perlu_reorder', true)->count();
        $belumAdaData = $bahanBakus->where('ss', null)->count();
    @endphp

    {{-- Stat Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">

        <div class="bg-emerald-700 rounded-2xl shadow-md shadow-emerald-200 px-5 py-5">
            <div class="w-9 h-9 rounded-xl bg-white/10 flex items-center justify-center mb-3">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
            </div>
            <p class="text-xs text-emerald-300 uppercase tracking-wider mb-1">Total Tracking</p>
            <p class="text-2xl font-bold text-white">{{ $totalTracking }} <span
                    class="text-sm font-medium text-emerald-300">Bahan</span></p>
            <p class="text-xs text-emerald-400 mt-1">Aktif dipantau sistem</p>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm px-5 py-5">
            <div class="w-9 h-9 rounded-xl bg-emerald-50 flex items-center justify-center mb-3">
                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <p class="text-xs text-slate-500 uppercase tracking-wider mb-1">Stok Aman</p>
            <p class="text-2xl font-bold text-slate-900">{{ $totalAman }} <span
                    class="text-sm font-medium text-slate-400">Bahan</span></p>
            <p class="text-xs text-emerald-600 font-medium mt-1">✓ Persediaan optimal</p>
        </div>

        <div
            class="bg-white rounded-2xl border {{ $totalReorder > 0 ? 'border-red-200' : 'border-slate-200' }} shadow-sm px-5 py-5 {{ $totalReorder > 0 ? 'bg-red-50/40' : '' }}">
            <div
                class="w-9 h-9 rounded-xl {{ $totalReorder > 0 ? 'bg-red-100' : 'bg-slate-100' }} flex items-center justify-center mb-3">
                <svg class="w-5 h-5 {{ $totalReorder > 0 ? 'text-red-600' : 'text-slate-400' }}" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>
            <p class="text-xs text-slate-500 uppercase tracking-wider mb-1">Perlu Reorder</p>
            <p class="text-2xl font-bold {{ $totalReorder > 0 ? 'text-red-600' : 'text-slate-900' }}">
                {{ $totalReorder }} <span class="text-sm font-medium text-slate-400">Bahan</span></p>
            <p class="text-xs {{ $totalReorder > 0 ? 'text-red-600 font-medium' : 'text-slate-400' }} mt-1">
                {{ $totalReorder > 0 ? '⚠ Butuh restock segera' : 'Semua aman' }}
            </p>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm px-5 py-5">
            <div class="w-9 h-9 rounded-xl bg-amber-50 flex items-center justify-center mb-3">
                <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <p class="text-xs text-slate-500 uppercase tracking-wider mb-1">Belum Ada Data</p>
            <p class="text-2xl font-bold text-slate-900">{{ $belumAdaData }} <span
                    class="text-sm font-medium text-slate-400">Bahan</span></p>
            <p class="text-xs text-amber-600 mt-1">Perlu data pemakaian</p>
        </div>
    </div>

    {{-- Rumus Perhitungan --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm px-6 py-5 mb-4">
        <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-4">Rumus Perhitungan</p>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div class="rounded-xl bg-amber-50 border border-amber-100 px-5 py-4">
                <p class="text-xs font-semibold text-amber-700 uppercase tracking-wider mb-2">Safety Stock</p>
                <p class="text-base font-bold text-amber-900 font-mono tracking-tight">SS = (Dmax × Lmax) − (D × L)</p>
            </div>
            <div class="rounded-xl bg-blue-50 border border-blue-100 px-5 py-4">
                <p class="text-xs font-semibold text-blue-700 uppercase tracking-wider mb-2">Reorder Point</p>
                <p class="text-base font-bold text-blue-900 font-mono tracking-tight">ROP = (D × L) + SS</p>
            </div>
        </div>
        <div class="flex flex-wrap gap-x-5 gap-y-1.5">
            @foreach ([
        'D' => 'Rata-rata pemakaian harian',
        'Dmax' => 'Pemakaian harian tertinggi',
        'L' => 'Rata-rata lead time (hari)',
        'Lmax' => 'Lead time terlama (hari)',
    ] as $var => $desc)
                <div class="flex items-center gap-1.5">
                    <span
                        class="font-mono text-xs font-bold text-slate-700 bg-slate-100 px-1.5 py-0.5 rounded">{{ $var }}</span>
                    <span class="text-xs text-slate-500">= {{ $desc }}</span>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Tabel Rincian --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h3 class="text-sm font-bold text-slate-900">Rincian Safety Stock & ROP</h3>
                <p class="text-xs text-slate-400 mt-0.5">Menampilkan {{ $totalTracking }} bahan baku dengan tracking
                    aktif</p>
            </div>
            <div class="flex items-center gap-4">
                <div class="hidden sm:flex items-center gap-3 text-xs text-slate-500">
                    <span class="flex items-center gap-1"><span
                            class="w-2 h-2 rounded-full bg-emerald-500 inline-block"></span> Aman</span>
                    <span class="flex items-center gap-1"><span
                            class="w-2 h-2 rounded-full bg-red-500 inline-block"></span> Perlu Reorder</span>
                    <span class="flex items-center gap-1"><span
                            class="w-2 h-2 rounded-full bg-amber-400 inline-block"></span> Belum Ada Data</span>
                </div>
                <a href="{{ route('app.bahan-baku.index') }}"
                    class="text-sm font-medium text-emerald-600 hover:text-emerald-700">
                    Kelola Bahan Baku →
                </a>
            </div>
        </div>

        @if ($bahanBakus->isEmpty())
            <div class="px-6 py-16 text-center">
                <div class="w-12 h-12 rounded-full bg-slate-100 flex items-center justify-center mx-auto mb-3">
                    <svg class="w-6 h-6 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10" />
                    </svg>
                </div>
                <p class="text-sm font-medium text-slate-700">Belum ada bahan baku dengan tracking aktif</p>
                <p class="text-xs text-slate-400 mt-1">Aktifkan tracking SS/ROP di halaman Bahan Baku</p>
                <a href="{{ route('app.bahan-baku.index') }}"
                    class="inline-block mt-4 px-4 py-2 rounded-lg bg-emerald-500 hover:bg-emerald-600 text-white text-sm font-medium transition">
                    Ke Halaman Bahan Baku
                </a>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-slate-50">
                        <tr class="text-left text-xs uppercase tracking-wide text-slate-500">
                            <th class="px-6 py-3 font-medium">Nama Bahan Baku</th>
                            <th class="px-6 py-3 font-medium text-right">D</th>
                            <th class="px-6 py-3 font-medium text-right">Dmax</th>
                            <th class="px-6 py-3 font-medium text-right">L (hari)</th>
                            <th class="px-6 py-3 font-medium text-right">Lmax</th>
                            <th class="px-6 py-3 font-medium text-right">
                                <span class="text-amber-600">Safety Stock</span>
                            </th>
                            <th class="px-6 py-3 font-medium text-right">
                                <span class="text-blue-600">ROP</span>
                            </th>
                            <th class="px-6 py-3 font-medium text-right">Stok Saat Ini</th>
                            <th class="px-6 py-3 font-medium text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($bahanBakus as $item)
                            <tr
                                class="hover:bg-slate-50/50 transition {{ $item->perlu_reorder ? 'bg-red-50/30' : '' }}">
                                <td class="px-6 py-4">
                                    <p class="text-sm font-semibold text-slate-900">{{ $item->nama }}</p>
                                    <p class="text-xs text-slate-400 capitalize">{{ $item->kategori_bb }} ·
                                        {{ $item->satuan }}</p>
                                </td>

                                @if ($item->ss !== null)
                                    <td class="px-6 py-4 text-sm text-right text-slate-600 tabular-nums">
                                        {{ $item->D }}</td>
                                    <td class="px-6 py-4 text-sm text-right text-slate-600 tabular-nums">
                                        {{ $item->Dmax }}</td>
                                    <td class="px-6 py-4 text-sm text-right text-slate-600 tabular-nums">
                                        {{ $item->L }}</td>
                                    <td class="px-6 py-4 text-sm text-right text-slate-600 tabular-nums">
                                        {{ $item->Lmax }}</td>
                                    <td class="px-6 py-4 text-right">
                                        <span
                                            class="text-sm font-bold text-amber-700 tabular-nums">{{ $item->ss }}</span>
                                        <span class="text-xs text-slate-400 ml-1">{{ $item->satuan }}</span>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <span
                                            class="text-sm font-bold text-blue-700 tabular-nums">{{ $item->rop }}</span>
                                        <span class="text-xs text-slate-400 ml-1">{{ $item->satuan }}</span>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <span
                                            class="text-sm font-bold tabular-nums {{ $item->perlu_reorder ? 'text-red-600' : 'text-slate-700' }}">
                                            {{ number_format($item->stok_saat_ini, 2, ',', '.') }}
                                        </span>
                                        <span class="text-xs text-slate-400 ml-1">{{ $item->satuan }}</span>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        @if ($item->perlu_reorder)
                                            <span
                                                class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700">
                                                ⚠ Perlu Reorder
                                            </span>
                                        @else
                                            <span
                                                class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-700">
                                                ✓ Aman
                                            </span>
                                        @endif
                                    </td>
                                @else
                                    <td colspan="7" class="px-6 py-4 text-center">
                                        <span
                                            class="text-xs text-amber-600 bg-amber-50 px-3 py-1.5 rounded-full border border-amber-100">
                                            Belum ada data pemakaian
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span
                                            class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-600">
                                            — Menunggu
                                        </span>
                                    </td>
                                @endif
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="px-6 py-3 border-t border-slate-100">
                <p class="text-xs text-slate-400">
                    Data dihitung otomatis dari riwayat bahan keluar dan lead time pengadaan
                </p>
            </div>
        @endif
    </div>

</x-layouts.admin>
