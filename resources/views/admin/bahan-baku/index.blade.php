<x-layouts.admin title="Bahan Baku" subtitle="Kelola bahan baku produksi Segar Alami.">

    {{-- Kartu Statistik --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm px-5 py-5 flex items-center gap-4">
            <div class="w-10 h-10 rounded-xl bg-slate-100 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                </svg>
            </div>
            <div>
                <p class="text-xs text-slate-500 uppercase tracking-wider">Total Bahan Utama</p>
                <p class="text-2xl font-bold text-slate-900">{{ $totalBB }} <span class="text-sm font-normal text-slate-400">Jenis</span></p>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm px-5 py-5 flex items-center gap-4">
            <div class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <p class="text-xs text-slate-500 uppercase tracking-wider">Stok Aman</p>
                <p class="text-2xl font-bold text-slate-900">{{ $totalAman }} <span class="text-sm font-normal text-slate-400">Bahan</span></p>
            </div>
        </div>

        <div class="rounded-2xl border shadow-sm px-5 py-5 flex items-center gap-4 {{ $totalKritis > 0 ? 'bg-red-50 border-red-200' : 'bg-white border-slate-200' }}">
            <div class="w-10 h-10 rounded-xl {{ $totalKritis > 0 ? 'bg-red-100' : 'bg-slate-100' }} flex items-center justify-center shrink-0">
                <svg class="w-5 h-5 {{ $totalKritis > 0 ? 'text-red-600' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                </svg>
            </div>
            <div>
                <p class="text-xs text-slate-500 uppercase tracking-wider">Perlu Restock</p>
                <p class="text-2xl font-bold {{ $totalKritis > 0 ? 'text-red-600' : 'text-slate-900' }}">{{ $totalKritis }} <span class="text-sm font-normal text-slate-400">Bahan</span></p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h3 class="text-sm font-bold text-slate-900">Daftar Bahan Baku</h3>
                <p class="text-xs text-slate-400 mt-0.5">Stok Minimum (Safety Stock) & ROP dihitung otomatis dari data pemakaian</p>
            </div>
            <a href="{{ route('app.bahan-baku.create') }}"
               class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg bg-emerald-500 hover:bg-emerald-600 text-white text-sm font-medium transition">
                + Tambah Bahan Baku
            </a>
        </div>

        <div class="px-6 py-3 border-b border-slate-100 flex gap-3">
            <form method="GET" action="{{ route('app.bahan-baku.index') }}" class="flex flex-1 gap-3">
                <div class="relative flex-1">
                    <svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M11 19a8 8 0 100-16 8 8 0 000 16z"/>
                    </svg>
                    <input type="search" name="q" value="{{ request('q') }}" placeholder="Cari nama bahan baku..."
                           class="w-full pl-10 px-3 py-2 rounded-lg border border-slate-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 focus:outline-none">
                </div>
                <select name="kategori_bb" onchange="this.form.submit()"
                        class="min-w-[160px] px-3 py-2 rounded-lg border border-slate-200 text-sm bg-white focus:border-emerald-500 focus:outline-none">
                    <option value="semua" @selected($kategori === 'semua')>Semua Kategori</option>
                    <option value="utama" @selected($kategori === 'utama')>Utama</option>
                    <option value="pendukung" @selected($kategori === 'pendukung')>Pendukung</option>
                </select>
                @if (request()->anyFilled(['q']) || $kategori !== 'utama')
                    <a href="{{ route('app.bahan-baku.index') }}" class="px-4 py-2 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-100 transition">Reset</a>
                @endif
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50">
                    <tr class="text-left text-xs uppercase tracking-wide text-slate-500">
                        <th class="px-6 py-3 font-medium">Nama Bahan Baku</th>
                        <th class="px-6 py-3 font-medium">Satuan</th>
                        <th class="px-6 py-3 font-medium text-right">Stok Saat Ini</th>
                        <th class="px-6 py-3 font-medium text-right">
                            <span class="text-amber-600">Stok Minimum</span>
                        </th>
                        <th class="px-6 py-3 font-medium text-right">
                            <span class="text-blue-600">ROP</span>
                        </th>
                        <th class="px-6 py-3 font-medium text-center">Status</th>
                        <th class="px-6 py-3 font-medium text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($bahanBakus as $item)
                        @php
                            $sr  = $ssRopData[$item->id] ?? null;
                            $ss  = ($sr && $sr['ss'] !== null) ? $sr['ss'] : null;
                            $rop = ($sr && $sr['rop'] !== null) ? $sr['rop'] : null;

                            // Status 3 zona berdasarkan SS (batas kritis) & ROP (titik pesan):
                            // - belum ada data SS  => status "—"
                            // - stok <= SS         => KRITIS
                            // - stok <= ROP        => PESAN ULANG
                            // - selain itu         => AMAN
                            if ($ss === null) {
                                $statusKey = 'none';
                            } elseif ($item->stok_saat_ini <= $ss) {
                                $statusKey = 'kritis';
                            } elseif ($rop !== null && $item->stok_saat_ini <= $rop) {
                                $statusKey = 'pesan';
                            } else {
                                $statusKey = 'aman';
                            }
                            $isKritis = $statusKey === 'kritis';
                        @endphp
                        <tr class="hover:bg-slate-50/50 {{ $isKritis ? 'bg-red-50/20' : '' }}">
                            <td class="px-6 py-4">
                                <p class="text-sm font-semibold text-slate-900">{{ $item->nama }}</p>
                                <span class="text-xs {{ $item->kategori_bb === 'utama' ? 'text-blue-600' : 'text-slate-400' }}">
                                    {{ ucfirst($item->kategori_bb) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-600">{{ $item->satuan }}</td>
                            <td class="px-6 py-4 text-sm text-right font-semibold tabular-nums {{ $isKritis ? 'text-red-600' : 'text-slate-700' }}">
                                {{ number_format($item->stok_saat_ini, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 text-right tabular-nums">
                                @if ($ss !== null)
                                    <span class="text-sm font-bold text-amber-700">{{ number_format($ss, 2, ',', '.') }}</span>
                                    <span class="text-xs text-slate-400 ml-0.5">{{ $item->satuan }}</span>
                                @else
                                    <span class="text-xs text-slate-400 italic">Belum ada data</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right tabular-nums">
                                @if ($rop !== null)
                                    <span class="text-sm font-bold text-blue-700">{{ number_format($rop, 2, ',', '.') }}</span>
                                    <span class="text-xs text-slate-400 ml-0.5">{{ $item->satuan }}</span>
                                @else
                                    <span class="text-xs text-slate-400">—</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if ($statusKey === 'kritis')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700">KRITIS</span>
                                @elseif ($statusKey === 'pesan')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-700">PESAN ULANG</span>
                                @elseif ($statusKey === 'aman')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-700">AMAN</span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-500">—</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="inline-flex items-center gap-3">
                                    <a href="{{ route('app.bahan-baku.edit', $item) }}" class="text-sm font-medium text-emerald-600 hover:text-emerald-700">Edit</a>
                                    <form method="POST" action="{{ route('app.bahan-baku.destroy', $item) }}" class="inline"
                                          data-confirm="Hapus {{ $item->nama }}?">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-sm font-medium text-red-500 hover:text-red-600">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-16 text-center text-sm text-slate-400">
                                Belum ada bahan baku.
                                <a href="{{ route('app.bahan-baku.create') }}" class="text-emerald-600 hover:underline">Tambahkan sekarang.</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($bahanBakus->hasPages())
            <div class="px-6 py-4 border-t border-slate-100 flex items-center justify-between">
                <p class="text-xs text-slate-400">
                    Menampilkan {{ $bahanBakus->firstItem() }}–{{ $bahanBakus->lastItem() }} dari {{ $bahanBakus->total() }} jenis bahan baku
                </p>
                {{ $bahanBakus->links() }}
            </div>
        @endif
    </div>

    {{-- Keterangan Rumus --}}
    <div class="mt-4 bg-slate-50 border border-slate-200 rounded-xl px-5 py-4">
        <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-3">Keterangan Rumus</p>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-3">
            <div class="rounded-lg bg-amber-50 border border-amber-100 px-4 py-3">
                <p class="text-xs font-semibold text-amber-700 mb-1">Stok Minimum (Safety Stock)</p>
                <p class="text-sm font-bold text-amber-900 font-mono">SS = (Dmaks × Lmaks) − (D × L)</p>
            </div>
            <div class="rounded-lg bg-blue-50 border border-blue-100 px-4 py-3">
                <p class="text-xs font-semibold text-blue-700 mb-1">Titik Pemesanan Ulang (ROP)</p>
                <p class="text-sm font-bold text-blue-900 font-mono">ROP = (D × L) + SS</p>
            </div>
        </div>
        <div class="flex flex-wrap gap-x-5 gap-y-1">
            @foreach (['D' => 'Rata-rata pemakaian harian', 'Dmaks' => 'Pemakaian harian tertinggi', 'L' => 'Rata-rata lead time (hari)', 'Lmaks' => 'Lead time terlama (hari)'] as $var => $desc)
                <div class="flex items-center gap-1.5">
                    <span class="font-mono text-xs font-bold text-slate-700 bg-slate-100 px-1.5 py-0.5 rounded">{{ $var }}</span>
                    <span class="text-xs text-slate-500">= {{ $desc }}</span>
                </div>
            @endforeach
        </div>
    </div>

</x-layouts.admin>
