<x-layouts.admin title="Varian Produk" subtitle="Kelola varian, ukuran, dan harga jual setiap produk.">

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <h3 class="text-sm font-bold text-slate-900">Daftar Varian Produk</h3>
            <a href="{{ route('app.varian-produk.create') }}"
                class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg bg-emerald-500 hover:bg-emerald-600 text-white text-sm font-medium transition">
                + Tambah Varian
            </a>
        </div>

        <div class="px-6 py-3 border-b border-slate-100 flex gap-3">
            <form method="GET" action="{{ route('app.varian-produk.index') }}" class="flex flex-1 gap-3">
                <div class="relative flex-1">
                    <svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-4.35-4.35M11 19a8 8 0 100-16 8 8 0 000 16z" />
                    </svg>
                    <input type="search" name="q" value="{{ request('q') }}"
                        placeholder="Cari nama varian atau produk..."
                        class="w-full pl-10 px-3 py-2 rounded-lg border border-slate-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 focus:outline-none">
                </div>
                <select name="produk_id" onchange="this.form.submit()"
                    class="min-w-[180px] px-3 py-2 rounded-lg border border-slate-200 text-sm bg-white focus:border-emerald-500 focus:outline-none">
                    <option value="">Semua Produk</option>
                    @foreach ($produks as $p)
                        <option value="{{ $p->id }}" @selected(request('produk_id') == $p->id)>{{ $p->nama }}</option>
                    @endforeach
                </select>
                @if (request()->anyFilled(['q', 'produk_id']))
                    <a href="{{ route('app.varian-produk.index') }}"
                        class="px-4 py-2 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-100 transition whitespace-nowrap">Reset</a>
                @endif
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50">
                    <tr class="text-left text-xs uppercase tracking-wide text-slate-500">
                        <th class="px-6 py-3 font-medium">Produk</th>
                        <th class="px-6 py-3 font-medium">
                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'nama_varian', 'dir' => $sort === 'nama_varian' && $dir === 'desc' ? 'asc' : 'desc']) }}"
                                class="inline-flex items-center gap-1 hover:text-slate-700">
                                Nama Varian
                                @if ($sort === 'nama_varian')
                                    <span class="text-emerald-600">{{ $dir === 'asc' ? '↑' : '↓' }}</span>
                                @endif
                            </a>
                        </th>
                        <th class="px-6 py-3 font-medium">Ukuran</th>
                        <th class="px-6 py-3 font-medium text-right">
                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'harga', 'dir' => $sort === 'harga' && $dir === 'desc' ? 'asc' : 'desc']) }}"
                                class="inline-flex items-center gap-1 hover:text-slate-700 justify-end w-full">
                                Harga
                                @if ($sort === 'harga')
                                    <span class="text-emerald-600">{{ $dir === 'asc' ? '↑' : '↓' }}</span>
                                @endif
                            </a>
                        </th>
                        <th class="px-6 py-3 font-medium text-center">
                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'stok', 'dir' => $sort === 'stok' && $dir === 'desc' ? 'asc' : 'desc']) }}"
                                class="inline-flex items-center gap-1 hover:text-slate-700">
                                Stok
                                @if ($sort === 'stok')
                                    <span class="text-emerald-600">{{ $dir === 'asc' ? '↑' : '↓' }}</span>
                                @else
                                    <span class="text-slate-300">⇅</span>
                                @endif
                            </a>
                        </th>
                        <th class="px-6 py-3 font-medium text-center">Status</th>
                        <th class="px-6 py-3 font-medium text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($variants as $v)
                        <tr class="hover:bg-slate-50/50">
                            <td class="px-6 py-4 text-sm text-slate-500">{{ $v->produk->nama }}</td>
                            <td class="px-6 py-4 text-sm font-semibold text-slate-900">{{ $v->nama_varian }}</td>
                            <td class="px-6 py-4 text-sm text-slate-500">{{ $v->ukuran ?? '—' }}</td>
                            <td class="px-6 py-4 text-right text-sm font-bold tabular-nums text-slate-900">
                                Rp {{ number_format($v->harga, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if ($v->stok <= 0)
                                    <span
                                        class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700">
                                        {{ $v->stok }} unit
                                    </span>
                                @elseif ($v->stok < 10)
                                    <span
                                        class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-700">
                                        {{ $v->stok }} unit
                                    </span>
                                @else
                                    <span class="text-sm font-semibold tabular-nums text-slate-700">
                                        {{ $v->stok }} unit
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if ($v->is_active)
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
                                    <a href="{{ route('app.varian-produk.edit', $v) }}"
                                        class="text-sm font-medium text-emerald-600 hover:text-emerald-700">Edit</a>
                                    <form method="POST" action="{{ route('app.varian-produk.destroy', $v) }}"
                                        class="inline" data-confirm="Hapus varian {{ $v->nama_varian }}?">
                                        @csrf @method('DELETE')
                                        <button type="submit"
                                            class="text-sm font-medium text-red-500 hover:text-red-600">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-16 text-center text-sm text-slate-400">
                                Belum ada varian.
                                <a href="{{ route('app.varian-produk.create') }}"
                                    class="text-emerald-600 hover:underline">Tambahkan sekarang.</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($variants->hasPages())
            <div class="px-6 py-4 border-t border-slate-100 flex items-center justify-between">
                <p class="text-xs text-slate-400">Menampilkan {{ $variants->firstItem() }}–{{ $variants->lastItem() }}
                    dari {{ $variants->total() }} varian</p>
                {{ $variants->links() }}
            </div>
        @endif
    </div>

</x-layouts.admin>
