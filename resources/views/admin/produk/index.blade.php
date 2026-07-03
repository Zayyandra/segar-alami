<x-layouts.admin title="Produk" subtitle="Kelola produk Segar Alami.">

    {{-- Stat Card --}}
    <div class="mb-6">
        <div class="bg-slate-50 border border-slate-200 rounded-2xl px-6 py-5 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div
                    class="w-10 h-10 rounded-xl bg-white border border-slate-200 flex items-center justify-center shadow-sm">
                    <svg class="w-5 h-5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
                <div>
                    <p class="text-xs text-slate-500 uppercase tracking-wider">Total Varian Produk</p>
                    <p class="text-3xl font-bold text-slate-900">{{ $totalVarian }}</p>
                </div>
            </div>
            <span
                class="text-xs font-semibold text-emerald-600 bg-emerald-50 px-3 py-1.5 rounded-full border border-emerald-200">
                Aktif
            </span>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <h3 class="text-sm font-bold text-slate-900">Katalog Produk</h3>
            <div class="flex items-center gap-2">
                <a href="{{ route('app.produk.create') }}"
                    class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg bg-emerald-500 hover:bg-emerald-600 text-white text-sm font-medium transition">
                    + Produk Baru
                </a>
            </div>
        </div>

        <div class="px-6 py-3 border-b border-slate-100 flex gap-3">
            <form method="GET" class="flex flex-1 gap-3">
                <div class="relative flex-1">
                    <svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-4.35-4.35M11 19a8 8 0 100-16 8 8 0 000 16z" />
                    </svg>
                    <input type="search" name="search" value="{{ request('search') }}"
                        placeholder="Cari nama produk..."
                        class="w-full pl-10 px-3 py-2 rounded-lg border border-slate-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 focus:outline-none">
                </div>
                <select name="kategori" onchange="this.form.submit()"
                    class="min-w-[180px] px-3 py-2 rounded-lg border border-slate-200 text-sm bg-white focus:border-emerald-500 focus:outline-none">
                    <option value="">Semua Kategori</option>
                    @foreach ($kategori as $k)
                        <option value="{{ $k->id }}" @selected(request('kategori') == $k->id)>{{ $k->nama }}</option>
                    @endforeach
                </select>
                @if (request()->anyFilled(['search', 'kategori']))
                    <a href="{{ route('app.produk.index') }}"
                        class="px-4 py-2 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-100 transition">Reset</a>
                @endif
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50">
                    <tr class="text-left text-xs uppercase tracking-wide text-slate-500">
                        <th class="px-6 py-3 font-medium">Nama Produk</th>
                        <th class="px-6 py-3 font-medium">Kategori</th>
                        <th class="px-6 py-3 font-medium text-center">Status Produk</th>
                        <th class="px-6 py-3 font-medium text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($produk as $item)
                        <tr class="hover:bg-slate-50/50">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    @if ($item->foto)
                                        <img src="{{ $item->foto_url }}" alt="{{ $item->nama }}"
                                            class="w-10 h-10 object-cover rounded-lg border border-slate-200 shrink-0">
                                    @else
                                        <div
                                            class="w-10 h-10 rounded-lg bg-slate-100 flex items-center justify-center shrink-0 text-slate-400 text-xs">
                                            —</div>
                                    @endif
                                    <div>
                                        <p class="text-sm font-semibold text-slate-900">{{ $item->nama }}</p>
                                        @if ($item->deskripsi)
                                            <p class="text-xs text-slate-400 line-clamp-1">{{ $item->deskripsi }}</p>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span
                                    class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-slate-100 text-slate-600">
                                    {{ $item->kategori->nama }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if ($item->is_active)
                                    <span
                                        class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-700">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Tersedia
                                    </span>
                                @else
                                    <span
                                        class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-500">
                                        <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span> Nonaktif
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="inline-flex items-center gap-3">
                                    <a href="{{ route('app.produk.edit', $item) }}"
                                        class="text-sm font-medium text-emerald-600 hover:text-emerald-700">Edit</a>
                                    <form method="POST" action="{{ route('app.produk.destroy', $item) }}"
                                        class="inline" data-confirm="Hapus produk ini?">
                                        @csrf @method('DELETE')
                                        <button type="submit"
                                            class="text-sm font-medium text-red-500 hover:text-red-600">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-16 text-center text-sm text-slate-400">
                                Belum ada produk. <a href="{{ route('app.produk.create') }}"
                                    class="text-emerald-600 hover:underline">Tambahkan sekarang.</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($produk->hasPages())
            <div class="px-6 py-4 border-t border-slate-100 flex items-center justify-between">
                <p class="text-xs text-slate-400">Menampilkan {{ $produk->firstItem() }}–{{ $produk->lastItem() }}
                    dari {{ $produk->total() }} produk</p>
                {{ $produk->links() }}
            </div>
        @endif
    </div>

</x-layouts.admin>
