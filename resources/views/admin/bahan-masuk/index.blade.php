<x-layouts.admin title="Bahan Masuk" subtitle="Monitoring arus bahan baku masuk ke gudang.">

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h3 class="text-sm font-bold text-slate-900">Stok Bahan Baku Masuk</h3>
                <p class="text-xs text-slate-400 mt-0.5">Monitoring arus masuk bahan baku secara real-time.</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('app.bahan-keluar.index') }}"
                   class="text-sm text-emerald-600 hover:text-emerald-700 font-medium">
                   Lihat Bahan Keluar →
                </a>
                <a href="{{ route('app.bahan-masuk.create') }}"
                   class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg bg-emerald-500 hover:bg-emerald-600 text-white text-sm font-medium transition">
                    + Catat Masuk
                </a>
            </div>
        </div>

        <div class="px-6 py-3 border-b border-slate-100 flex gap-3">
            <form method="GET" action="{{ route('app.bahan-masuk.index') }}" class="flex flex-1 gap-3 flex-wrap">
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
                    <a href="{{ route('app.bahan-masuk.index') }}" class="px-4 py-2 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-100 transition">Reset</a>
                @endif
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50">
                    <tr class="text-left text-xs uppercase tracking-wide text-slate-500">
                        <th class="px-6 py-3 font-medium">Bahan Baku</th>
                        <th class="px-6 py-3 font-medium">Vendor / Supplier</th>
                        <th class="px-6 py-3 font-medium text-right">Jumlah</th>
                        <th class="px-6 py-3 font-medium text-center">Lead Time</th>
                        <th class="px-6 py-3 font-medium">Tgl Kadaluarsa</th>
                        <th class="px-6 py-3 font-medium">Tanggal</th>
                        <th class="px-6 py-3 font-medium text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($bahanMasuks as $item)
                        @php
                            $isKadaluarsa = $item->tanggal_kadaluarsa && $item->tanggal_kadaluarsa->isPast();
                            $mendekati    = $item->tanggal_kadaluarsa && !$isKadaluarsa && $item->tanggal_kadaluarsa->diffInDays(now()) <= 7;
                        @endphp
                        <tr class="hover:bg-slate-50/50 {{ $isKadaluarsa ? 'bg-red-50/20' : '' }}">
                            <td class="px-6 py-4">
                                <p class="text-sm font-semibold text-slate-900">{{ $item->bahanBaku->nama }}</p>
                                <p class="text-xs text-slate-400">{{ ucfirst($item->bahanBaku->kategori_bb) }}</p>
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-600">{{ $item->nama_supplier ?? '—' }}</td>
                            <td class="px-6 py-4 text-right">
                                <span class="text-sm font-bold text-emerald-600 tabular-nums">+{{ number_format($item->jumlah, 0, ',', '.') }}</span>
                                <span class="text-xs text-slate-400 ml-1">{{ $item->bahanBaku->satuan }}</span>
                            </td>
                            <td class="px-6 py-4 text-center text-sm text-slate-600">
                                {{ $item->lead_time_hari !== null ? $item->lead_time_hari . ' hari' : '—' }}
                            </td>
                            <td class="px-6 py-4 text-sm">
                                @if ($item->tanggal_kadaluarsa)
                                    @if ($isKadaluarsa)
                                        <span class="text-red-600 font-semibold text-xs">⚠ {{ $item->tanggal_kadaluarsa->format('d M Y') }} (Kadaluarsa)</span>
                                    @elseif ($mendekati)
                                        <span class="text-amber-600 font-semibold text-xs">⚠ {{ $item->tanggal_kadaluarsa->format('d M Y') }}</span>
                                    @else
                                        <span class="text-slate-600 text-xs">{{ $item->tanggal_kadaluarsa->format('d M Y') }}</span>
                                    @endif
                                @else
                                    <span class="text-slate-300 text-xs">—</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-500">{{ $item->tanggal->format('d M Y') }}</td>
                            <td class="px-6 py-4 text-right">
                                <div class="inline-flex items-center gap-3">
                                    <a href="{{ route('app.bahan-masuk.edit', $item) }}" class="text-sm font-medium text-emerald-600 hover:text-emerald-700">Edit</a>
                                    <form method="POST" action="{{ route('app.bahan-masuk.destroy', $item) }}" class="inline"
                                          onsubmit="return confirm('Hapus data ini? Stok akan dikurangi {{ number_format($item->jumlah, 0) }} {{ $item->bahanBaku->satuan }}.')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-sm font-medium text-red-500 hover:text-red-600">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-16 text-center text-sm text-slate-400">
                                Belum ada data bahan masuk.
                                <a href="{{ route('app.bahan-masuk.create') }}" class="text-emerald-600 hover:underline">Catat sekarang.</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($bahanMasuks->hasPages())
            <div class="px-6 py-4 border-t border-slate-100">{{ $bahanMasuks->links() }}</div>
        @endif
    </div>

</x-layouts.admin>
