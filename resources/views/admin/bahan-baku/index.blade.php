<x-layouts.admin title="Bahan Baku" subtitle="Kelola bahan baku produksi Segar Alami.">

    {{-- Stat Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm px-5 py-5 flex items-center gap-4">
            <div class="w-10 h-10 rounded-xl bg-slate-100 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                </svg>
            </div>
            <div>
                <p class="text-xs text-slate-500 uppercase tracking-wider">Total Bahan Baku</p>
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
                <p class="text-2xl font-bold text-slate-900">{{ $totalAman }} <span class="text-sm font-normal text-slate-400">bahan</span></p>
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
                <p class="text-2xl font-bold {{ $totalKritis > 0 ? 'text-red-600' : 'text-slate-900' }}">{{ $totalKritis }} <span class="text-sm font-normal text-slate-400">bahan</span></p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <h3 class="text-sm font-bold text-slate-900">Daftar Bahan Baku</h3>
            <a href="{{ route('app.bahan-baku.create') }}"
               class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg bg-emerald-500 hover:bg-emerald-600 text-white text-sm font-medium transition">
                + Tambah Bahan Baku
            </a>
        </div>

        @if (session('success'))
            <div class="px-6 pt-4">
                <div class="rounded-md border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
                    {{ session('success') }}
                </div>
            </div>
        @endif

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
                    <option value="">Semua Kategori</option>
                    <option value="utama" @selected(request('kategori_bb') === 'utama')>Utama</option>
                    <option value="pendukung" @selected(request('kategori_bb') === 'pendukung')>Pendukung</option>
                </select>
                @if (request()->anyFilled(['q', 'kategori_bb']))
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
                        <th class="px-6 py-3 font-medium text-right">Stok Minimum</th>
                        <th class="px-6 py-3 font-medium text-center">Status</th>
                        <th class="px-6 py-3 font-medium text-center">SS & ROP</th>
                        <th class="px-6 py-3 font-medium text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($bahanBakus as $item)
                        @php
                            $statusStok = $item->status_stok ?? ($item->stok_saat_ini <= $item->stok_minimum ? 'kritis' : 'aman');
                            $sr = $ssRopData[$item->id] ?? null;
                        @endphp
                        <tr class="hover:bg-slate-50/50 {{ $statusStok === 'kritis' ? 'bg-red-50/20' : '' }}">
                            <td class="px-6 py-4">
                                <p class="text-sm font-semibold text-slate-900">{{ $item->nama }}</p>
                                <span class="text-xs {{ $item->kategori_bb === 'utama' ? 'text-blue-600' : 'text-slate-400' }}">
                                    {{ ucfirst($item->kategori_bb) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-600">{{ $item->satuan }}</td>
                            <td class="px-6 py-4 text-sm text-right font-semibold tabular-nums {{ $statusStok === 'kritis' ? 'text-red-600' : 'text-slate-700' }}">
                                {{ number_format($item->stok_saat_ini, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 text-sm text-right text-slate-400 tabular-nums">
                                {{ number_format($item->stok_minimum, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if ($statusStok === 'kritis')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700">KRITIS</span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-700">AMAN</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center text-xs">
                                @if ($item->tracking_ss_rop && $sr && $sr['ss'] !== null)
                                    <div class="space-y-0.5">
                                        <div><span class="text-amber-600 font-semibold">SS</span> <span class="text-slate-600">{{ $sr['ss'] }} {{ $item->satuan }}</span></div>
                                        <div><span class="text-blue-600 font-semibold">ROP</span> <span class="text-slate-600">{{ $sr['rop'] }} {{ $item->satuan }}</span></div>
                                    </div>
                                @elseif ($item->tracking_ss_rop)
                                    <span class="text-amber-500">Aktif — belum ada data</span>
                                @else
                                    <span class="text-slate-300">—</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="inline-flex items-center gap-3">
                                    <a href="{{ route('app.bahan-baku.edit', $item) }}" class="text-sm font-medium text-emerald-600 hover:text-emerald-700">Edit</a>
                                    <form method="POST" action="{{ route('app.bahan-baku.destroy', $item) }}" class="inline" onsubmit="return confirm('Hapus {{ $item->nama }}?')">
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
                <p class="text-xs text-slate-400">Menampilkan {{ $bahanBakus->firstItem() }}–{{ $bahanBakus->lastItem() }} dari {{ $bahanBakus->total() }} jenis bahan baku</p>
                {{ $bahanBakus->links() }}
            </div>
        @endif
    </div>

</x-layouts.admin>
