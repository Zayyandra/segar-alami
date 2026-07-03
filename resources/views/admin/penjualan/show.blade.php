<x-layouts.admin title="Detail Transaksi" subtitle="Informasi lengkap tentang transaksi penjualan Segar Alami.">
    <div class="max-w-3xl mx-auto space-y-5">

        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">

            <div class="px-8 py-5 border-b border-slate-100 grid md:grid-cols-3 gap-4">
                <div>
                    <p class="text-xs text-slate-400 uppercase tracking-wider mb-1">Tanggal</p>
                    <p class="text-sm font-medium text-slate-800">{{ $penjualan->tanggal->format('d M Y') }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate-400 uppercase tracking-wider mb-1">Dicatat Oleh</p>
                    <p class="text-sm font-medium text-slate-800">{{ $penjualan->user->name }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate-400 uppercase tracking-wider mb-1">Keterangan</p>
                    <p class="text-sm font-medium text-slate-800">{{ $penjualan->keterangan ?? '—' }}</p>
                </div>
            </div>

            <div class="overflow-x-auto">
            <table class="w-full min-w-[560px]">
                <thead class="bg-slate-50">
                    <tr class="text-left text-xs uppercase tracking-wide text-slate-500">
                        <th class="px-6 py-3 font-medium">Produk</th>
                        <th class="px-6 py-3 font-medium text-center">Jumlah</th>
                        <th class="px-6 py-3 font-medium text-right">Harga Satuan</th>
                        <th class="px-6 py-3 font-medium text-right">Subtotal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach ($penjualan->details as $detail)
                        <tr>
                            <td class="px-6 py-4">
                                <p class="text-sm font-medium text-slate-900">{{ $detail->varianProduk->produk->nama }}</p>
                                <p class="text-xs text-slate-500">
                                    {{ $detail->varianProduk->nama_varian }}
                                    @if ($detail->varianProduk->ukuran)
                                        · {{ $detail->varianProduk->ukuran }}
                                    @endif
                                </p>
                            </td>
                            <td class="px-6 py-4 text-center text-sm text-slate-700">{{ $detail->jumlah }}</td>
                            <td class="px-6 py-4 text-right text-sm tabular-nums text-slate-700">
                                Rp {{ number_format($detail->harga_satuan, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 text-right text-sm font-semibold tabular-nums text-slate-900">
                                Rp {{ number_format($detail->sub_total, 0, ',', '.') }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="border-t-2 border-slate-200">
                        <td colspan="3" class="px-6 py-4 text-right text-sm font-semibold text-slate-700">Total</td>
                        <td class="px-6 py-4 text-right text-base font-bold text-emerald-600">
                            Rp {{ number_format($penjualan->total, 0, ',', '.') }}
                        </td>
                    </tr>
                </tfoot>
            </table>
            </div>

            <div class="px-8 py-4 border-t border-slate-100 flex justify-end gap-3">
                <a href="{{ route('app.penjualan.index') }}"
                   class="px-5 py-2 rounded-lg border border-slate-200 text-sm font-medium text-slate-700 hover:bg-slate-50 transition">
                    Kembali
                </a>
                <a href="{{ route('app.penjualan.edit', $penjualan) }}"
                   class="px-5 py-2 rounded-lg bg-emerald-500 hover:bg-emerald-600 text-white text-sm font-medium transition">
                    Edit Transaksi
                </a>
            </div>
        </div>

        {{-- Estimasi Pemakaian Bahan Baku --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-8 py-5 border-b border-slate-100">
                <p class="text-sm font-semibold text-slate-700">Estimasi Pemakaian Bahan Baku</p>
                <p class="text-xs text-slate-400 mt-0.5">Dihitung otomatis berdasarkan rasio konversi produk</p>
            </div>

            @if(count($estimasi) > 0)
                <table class="w-full">
                    <thead class="bg-slate-50">
                        <tr class="text-left text-xs uppercase tracking-wide text-slate-500">
                            <th class="px-6 py-3 font-medium">Bahan Baku</th>
                            <th class="px-6 py-3 font-medium text-right">Estimasi Terpakai</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($estimasi as $item)
                            <tr>
                                <td class="px-6 py-4 text-sm text-slate-700">{{ $item['nama'] }}</td>
                                <td class="px-6 py-4 text-right text-sm font-medium text-slate-900 tabular-nums">
                                    {{ number_format($item['total'], 2) }} {{ $item['satuan'] }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="px-8 py-5 text-sm text-slate-400">
                    Belum ada data konversi untuk produk-produk di transaksi ini.
                </div>
            @endif
        </div>

    </div>
</x-layouts.admin>
