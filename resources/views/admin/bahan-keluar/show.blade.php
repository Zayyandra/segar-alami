<x-layouts.admin title="Detail Bahan Keluar">
    <div class="max-w-3xl mx-auto space-y-5">

        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm px-8 py-6">
            <div class="flex items-start justify-between mb-6">
                <div>
                    <h2 class="text-lg font-bold text-slate-900">{{ $bahanKeluar->bahanBaku->nama }}</h2>
                    <p class="text-sm text-slate-500">{{ $bahanKeluar->tanggal->format('d M Y') }}</p>
                </div>
                <a href="{{ route('app.bahan-keluar.index') }}"
                    class="text-sm font-medium text-slate-500 hover:text-slate-700">← Kembali</a>
            </div>

            <div class="grid grid-cols-2 gap-5 pb-6 border-b border-slate-100">
                <div>
                    <p class="text-xs uppercase tracking-wide text-slate-400 mb-1">Jumlah Keluar</p>
                    <p class="text-xl font-bold text-slate-900">
                        {{ number_format($bahanKeluar->jumlah, 2) }} {{ $bahanKeluar->bahanBaku->satuan }}
                    </p>
                </div>
                @if ($bahanKeluar->varianProduk)
                    <div>
                        <p class="text-xs uppercase tracking-wide text-slate-400 mb-1">Hasil Produksi</p>
                        <p class="text-xl font-bold text-emerald-600">
                            {{ $bahanKeluar->hasil_produksi ?? 0 }} unit
                            <span class="text-sm font-normal text-slate-500">
                                — {{ $bahanKeluar->varianProduk->produk->nama }} ({{ $bahanKeluar->varianProduk->nama_varian }})
                            </span>
                        </p>
                    </div>
                @endif
            </div>

            <div class="pt-5">
                <h3 class="text-sm font-semibold text-slate-700 uppercase tracking-wider mb-3">
                    Diambil dari Batch (Urutan FEFO)
                </h3>

                @if ($bahanKeluar->pemakaianBatch->isEmpty())
                    <p class="text-sm text-slate-400 italic">
                        Tidak ada catatan batch spesifik (kemungkinan transaksi lama sebelum fitur FEFO batch aktif).
                    </p>
                @else
                    <div class="space-y-2">
                        @foreach ($bahanKeluar->pemakaianBatch as $p)
                            <div class="flex items-center justify-between rounded-lg border border-slate-100 px-4 py-3 bg-slate-50/50">
                                <div class="flex items-center gap-3">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-md bg-white border border-slate-200 font-mono text-xs font-bold text-slate-700">
                                        {{ $p->bahanMasuk->kode_batch ?? '—' }}
                                    </span>
                                    <div>
                                        <p class="text-xs text-slate-400">
                                            Masuk: {{ $p->bahanMasuk->tanggal->format('d M Y') }}
                                            @if ($p->bahanMasuk->tanggal_kadaluarsa)
                                                &middot; Exp: {{ $p->bahanMasuk->tanggal_kadaluarsa->format('d M Y') }}
                                            @endif
                                        </p>
                                    </div>
                                </div>
                                <span class="text-sm font-semibold text-slate-700 tabular-nums">
                                    {{ number_format($p->jumlah_diambil, 2) }} {{ $bahanKeluar->bahanBaku->satuan }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            @if ($bahanKeluar->keterangan)
                <div class="mt-5 pt-5 border-t border-slate-100">
                    <p class="text-xs uppercase tracking-wide text-slate-400 mb-1">Keterangan</p>
                    <p class="text-sm text-slate-600">{{ $bahanKeluar->keterangan }}</p>
                </div>
            @endif
        </div>

    </div>
</x-layouts.admin>
