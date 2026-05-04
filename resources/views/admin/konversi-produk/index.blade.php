{{-- resources/views/admin/konversi-produk/index.blade.php --}}
<x-layouts.admin title="Konversi Produk" subtitle="Atur rasio kebutuhan bahan baku per satuan varian produk.">

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <h3 class="text-sm font-bold text-slate-900">Daftar Konversi Produk</h3>
            <a href="{{ route('app.konversi-produk.create') }}"
                class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg bg-emerald-500 hover:bg-emerald-600 text-white text-sm font-medium transition">
                + Tambah Konversi
            </a>
        </div>

        <div class="px-6 py-3 border-b border-slate-100">
            <form method="GET" action="{{ route('app.konversi-produk.index') }}" class="flex gap-3">
                <select name="varian_produk_id" onchange="this.form.submit()"
                    class="min-w-[220px] px-3 py-2 rounded-lg border border-slate-200 text-sm bg-white focus:border-emerald-500 focus:outline-none">
                    <option value="">Semua Varian Produk</option>
                    @foreach ($varianProduks as $varian)
                        <option value="{{ $varian->id }}" @selected(request('varian_produk_id') == $varian->id)>
                            {{ $varian->produk->nama }} —
                            {{ $varian->nama_varian }}{{ $varian->ukuran ? ' (' . $varian->ukuran . ')' : '' }}
                        </option>
                    @endforeach
                </select>
                @if (request()->filled('varian_produk_id'))
                    <a href="{{ route('app.konversi-produk.index') }}"
                        class="px-4 py-2 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-100 transition">Reset</a>
                @endif
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50">
                    <tr class="text-left text-xs uppercase tracking-wide text-slate-500">
                        <th class="px-6 py-3 font-medium">Varian Produk</th>
                        <th class="px-6 py-3 font-medium">Bahan Baku</th>
                        <th class="px-6 py-3 font-medium text-right">Jumlah per Satuan</th>
                        <th class="px-6 py-3 font-medium text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($konversiProduks as $item)
                        <tr class="hover:bg-slate-50/50">
                            <td class="px-6 py-4">
                                <p class="text-sm font-semibold text-slate-900">{{ $item->varianProduk->produk->nama }}
                                </p>
                                <p class="text-xs text-slate-400">
                                    {{ $item->varianProduk->nama_varian }}{{ $item->varianProduk->ukuran ? ' (' . $item->varianProduk->ukuran . ')' : '' }}
                                </p>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-sm text-slate-700">{{ $item->bahanBaku->nama }}</p>
                                <p class="text-xs text-slate-400">{{ $item->bahanBaku->satuan }}</p>
                            </td>
                            <td class="px-6 py-4 text-sm text-right tabular-nums text-slate-700">
                                {{ rtrim(rtrim(number_format($item->jumlah_per_satuan, 4, '.', ''), '0'), '.') }}
                                <span class="text-slate-400 text-xs">{{ $item->bahanBaku->satuan }}</span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="inline-flex items-center gap-3">
                                    <a href="{{ route('app.konversi-produk.edit', $item) }}"
                                        class="text-sm font-medium text-emerald-600 hover:text-emerald-700">Edit</a>
                                    <form method="POST" action="{{ route('app.konversi-produk.destroy', $item) }}"
                                        class="inline" onsubmit="return confirm('Hapus konversi ini?')">
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
                                Belum ada data konversi produk.
                                <a href="{{ route('app.konversi-produk.create') }}"
                                    class="text-emerald-600 hover:underline">Tambahkan sekarang.</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($konversiProduks->hasPages())
            <div class="px-6 py-4 border-t border-slate-100 flex items-center justify-between">
                <p class="text-xs text-slate-400">Menampilkan
                    {{ $konversiProduks->firstItem() }}–{{ $konversiProduks->lastItem() }} dari
                    {{ $konversiProduks->total() }} konversi</p>
                {{ $konversiProduks->links() }}
            </div>
        @endif
    </div>

</x-layouts.admin>
