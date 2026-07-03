<x-layouts.admin title="Kategori Produk" subtitle="Kelola kategori produk Segar Alami.">

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <h3 class="text-sm font-bold text-slate-900">Daftar Kategori</h3>
            <a href="{{ route('app.kategori.create') }}"
                class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg bg-emerald-500 hover:bg-emerald-600 text-white text-sm font-medium transition">
                + Tambah Kategori
            </a>
        </div>

        @if (session('success'))
            <div class="px-6 pt-4">
                <div class="rounded-md border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
                    {{ session('success') }}
                </div>
            </div>
        @endif

        @if (session('error'))
            <div class="px-6 pt-4">
                <div class="rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                    {{ session('error') }}
                </div>
            </div>
        @endif

        <div class="px-6 py-3 border-b border-slate-100">
            <form method="GET" class="max-w-sm">
                <div class="relative">
                    <svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-4.35-4.35M11 19a8 8 0 100-16 8 8 0 000 16z" />
                    </svg>
                    <input type="search" name="search" value="{{ request('search') }}" placeholder="Cari kategori..."
                        class="w-full pl-10 px-3 py-2 rounded-lg border border-slate-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 focus:outline-none">
                </div>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50">
                    <tr class="text-left text-xs uppercase tracking-wide text-slate-500">
                        <th class="px-6 py-3 font-medium w-20">Urutan</th>
                        <th class="px-6 py-3 font-medium">Nama Kategori</th>
                        <th class="px-6 py-3 font-medium text-center">Status</th>
                        <th class="px-6 py-3 font-medium text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($kategori as $item)
                        <tr class="hover:bg-slate-50/50">
                            <td class="px-6 py-4 text-sm text-slate-400 tabular-nums">{{ $item->urutan }}</td>
                            <td class="px-6 py-4 text-sm font-semibold text-slate-900">{{ $item->nama }}</td>
                            <td class="px-6 py-4 text-center">
                                @if ($item->is_active)
                                    <span
                                        class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-700">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Aktif
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
                                    <a href="{{ route('app.kategori.edit', $item) }}"
                                        class="text-sm font-medium text-emerald-600 hover:text-emerald-700">Edit</a>
                                    <form method="POST" action="{{ route('app.kategori.destroy', $item) }}"
                                        class="inline" data-confirm="Hapus kategori ini?">
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
                                Belum ada kategori.
                                <a href="{{ route('app.kategori.create') }}"
                                    class="text-emerald-600 hover:underline">Tambahkan sekarang.</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($kategori->hasPages())
            <div class="px-6 py-4 border-t border-slate-100">{{ $kategori->links() }}</div>
        @endif
    </div>

</x-layouts.admin>
