<x-layouts.admin title="Bahan Keluar" subtitle="Monitoring pemakaian bahan baku untuk produksi.">

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h3 class="text-sm font-bold text-slate-900">Stok Bahan Baku Keluar</h3>
                <p class="text-xs text-slate-400 mt-0.5">Monitoring pemakaian bahan baku secara real-time.</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('app.bahan-masuk.index') }}"
                    class="text-sm text-emerald-600 hover:text-emerald-700 font-medium">
                    Lihat Bahan Masuk →
                </a>
                <a href="{{ route('app.bahan-keluar.create') }}"
                    class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg bg-emerald-500 hover:bg-emerald-600 text-white text-sm font-medium transition">
                    + Catat Keluar
                </a>
            </div>
        </div>

        <div class="px-6 py-3 border-b border-slate-100 flex gap-3">
            <form method="GET" action="{{ route('app.bahan-keluar.index') }}" class="flex flex-1 gap-3 flex-wrap">
                <select name="bahan_baku_id" onchange="this.form.submit()"
                    class="min-w-[200px] px-3 py-2 rounded-lg border border-slate-200 text-sm bg-white focus:border-emerald-500 focus:outline-none">
                    <option value="">Semua Bahan Baku</option>
                    @foreach ($bahanBakus as $bb)
                        <option value="{{ $bb->id }}" @selected(request('bahan_baku_id') == $bb->id)>{{ $bb->nama }}</option>
                    @endforeach
                </select>
                <input type="date" name="tanggal" value="{{ request('tanggal') }}" onchange="this.form.submit()"
                    class="px-3 py-2 rounded-lg border border-slate-200 text-sm bg-white focus:border-emerald-500 focus:outline-none">
                @if (request()->anyFilled(['bahan_baku_id', 'tanggal']))
                    <a href="{{ route('app.bahan-keluar.index') }}"
                        class="px-4 py-2 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-100 transition">Reset</a>
                @endif
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50">
                    <tr class="text-left text-xs uppercase tracking-wide text-slate-500">
                        <th class="px-6 py-3 font-medium">Bahan Baku</th>
                        <th class="px-6 py-3 font-medium">Batch Terpakai</th>
                        <th class="px-6 py-3 font-medium text-right">Jumlah Keluar</th>
                        <th class="px-6 py-3 font-medium">Keterangan</th>
                        <th class="px-6 py-3 font-medium">Dicatat Oleh</th>
                        <th class="px-6 py-3 font-medium">Tanggal</th>
                        <th class="px-6 py-3 font-medium text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($bahanKeluars as $item)
                        <tr class="hover:bg-slate-50/50">
                            <td class="px-6 py-4">
                                <p class="text-sm font-semibold text-slate-900">{{ $item->bahanBaku->nama }}</p>
                                <p class="text-xs text-slate-400">{{ ucfirst($item->bahanBaku->kategori_bb) }}</p>
                            </td>
                            <td class="px-6 py-4">
                                @php $jumlahBatch = $item->pemakaianBatch->count(); @endphp
                                @if ($jumlahBatch === 0)
                                    <span class="text-slate-300 text-xs italic">— (data lama)</span>
                                @elseif ($jumlahBatch === 1)
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-md bg-slate-100 border border-slate-200 font-mono text-xs font-bold text-slate-700">
                                        {{ $item->pemakaianBatch->first()->bahanMasuk->kode_batch ?? '—' }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-md bg-amber-50 border border-amber-200 text-xs font-semibold text-amber-700">
                                        {{ $jumlahBatch }} batch
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <span
                                    class="text-sm font-bold text-red-500 tabular-nums">−{{ number_format($item->jumlah, 0, ',', '.') }}</span>
                                <span class="text-xs text-slate-400 ml-1">{{ $item->bahanBaku->satuan }}</span>
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-600">{{ $item->keterangan ?? '—' }}</td>
                            <td class="px-6 py-4 text-sm text-slate-500">{{ $item->user->name ?? '—' }}</td>
                            <td class="px-6 py-4 text-sm text-slate-500">{{ $item->tanggal->format('d M Y') }}</td>
                            <td class="px-6 py-4 text-right">
                                <div class="inline-flex items-center gap-3">
                                    <a href="{{ route('app.bahan-keluar.show', $item) }}"
                                        class="text-sm font-medium text-slate-500 hover:text-slate-700">Detail</a>
                                    <a href="{{ route('app.bahan-keluar.edit', $item) }}"
                                        class="text-sm font-medium text-emerald-600 hover:text-emerald-700">Edit</a>
                                    <form method="POST" action="{{ route('app.bahan-keluar.destroy', $item) }}"
                                        class="inline"
                                        data-confirm="Hapus? Stok akan dikembalikan {{ number_format($item->jumlah, 0) }} {{ $item->bahanBaku->satuan }}.">
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
                                Belum ada data bahan keluar.
                                <a href="{{ route('app.bahan-keluar.create') }}"
                                    class="text-emerald-600 hover:underline">Catat sekarang.</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($bahanKeluars->hasPages())
            <div class="px-6 py-4 border-t border-slate-100">{{ $bahanKeluars->links() }}</div>
        @endif
    </div>

</x-layouts.admin>
