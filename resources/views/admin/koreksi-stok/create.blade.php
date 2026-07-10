<x-layouts.admin title="Koreksi Stok" subtitle="Sesuaikan stok bahan baku dengan alasan resmi yang tercatat.">
    <div class="max-w-2xl mx-auto space-y-5">

        @if ($errors->any())
            <div class="rounded-lg border border-red-100 bg-red-50 px-4 py-3 text-sm text-red-700">
                <p class="font-medium mb-1">Terdapat kesalahan:</p>
                <ul class="list-disc list-inside space-y-0.5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm px-8 py-7">
            <div class="mb-6 pb-6 border-b border-slate-100">
                <h2 class="text-lg font-bold text-slate-900">{{ $bahanBaku->nama }}</h2>
                <p class="text-sm text-slate-500 mt-1">
                    Stok saat ini di sistem:
                    <span class="font-bold text-slate-700">{{ number_format($bahanBaku->stok_saat_ini, 2) }} {{ $bahanBaku->satuan }}</span>
                </p>
            </div>

            <form method="POST" action="{{ route('app.koreksi-stok.store', $bahanBaku) }}">
                @csrf

                <div class="space-y-5">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">
                            Stok Sesudah Koreksi <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="number" name="stok_sesudah" step="0.01" min="0"
                                value="{{ old('stok_sesudah', $bahanBaku->stok_saat_ini) }}"
                                class="w-full rounded-lg border border-slate-200 px-3 py-2.5 pr-16 text-sm
                                       focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 focus:outline-none">
                            <span class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-xs text-slate-400">
                                {{ $bahanBaku->satuan }}
                            </span>
                        </div>
                        <p class="mt-1 text-xs text-slate-400">Masukkan hasil hitung fisik/aktual bahan baku ini.</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">
                            Alasan Koreksi <span class="text-red-500">*</span>
                        </label>
                        <select name="alasan"
                            class="w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm bg-white
                                   focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 focus:outline-none">
                            <option value="">— Pilih alasan —</option>
                            @foreach (\App\Models\KoreksiStok::ALASAN_OPTIONS as $key => $label)
                                <option value="{{ $key }}" @selected(old('alasan') === $key)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">
                            Keterangan Tambahan <span class="text-slate-400 font-normal">(opsional)</span>
                        </label>
                        <textarea name="keterangan" rows="3"
                            placeholder="Jelaskan detail koreksi ini, misal: hasil stok opname tanggal 10 Juli menemukan selisih..."
                            class="w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm
                                   focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 focus:outline-none">{{ old('keterangan') }}</textarea>
                    </div>
                </div>

                <div class="mt-8 flex items-center justify-end gap-3 border-t border-slate-100 pt-6">
                    <a href="{{ route('app.bahan-baku.index') }}"
                        class="px-4 py-2 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-100 transition">Batal</a>
                    <button type="submit"
                        class="px-5 py-2 rounded-lg bg-amber-500 hover:bg-amber-600 text-white text-sm font-medium transition">
                        Simpan Koreksi
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.admin>
