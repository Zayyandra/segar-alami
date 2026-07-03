@csrf

<div class="space-y-5" x-data="{
    satuanMap: {{ Js::from($bahanBakus->pluck('satuan', 'id')) }},
    items: {{ isset($existingKonversi) && $existingKonversi->count()
        ? Js::from($existingKonversi->map(fn($k) => [
            'bahan_baku_id'     => (string) $k->bahan_baku_id,
            'jumlah_per_satuan' => (string) $k->jumlah_per_satuan,
          ]))
        : '[{ bahan_baku_id: \'\', jumlah_per_satuan: \'\' }]'
    }},
    addItem() {
        this.items.push({ bahan_baku_id: '', jumlah_per_satuan: '' })
    },
    removeItem(index) {
        if (this.items.length > 1) this.items.splice(index, 1)
    },
    getSatuan(id) {
        return this.satuanMap[id] ?? '—'
    }
}">

    {{-- Varian Produk --}}
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1.5">
            Varian Produk <span class="text-red-500">*</span>
        </label>

        @if(isset($konversiProduk) && $konversiProduk->exists)
            {{-- Edit mode: tampilkan nama, kirim via hidden --}}
            <input type="hidden" name="varian_produk_id" value="{{ $konversiProduk->varian_produk_id }}">
            <div class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm text-slate-600">
                {{ $konversiProduk->varianProduk->produk->nama }} —
                {{ $konversiProduk->varianProduk->nama_varian }}
                {{ $konversiProduk->varianProduk->ukuran ? '(' . $konversiProduk->varianProduk->ukuran . ')' : '' }}
            </div>
        @else
            {{-- Create mode: dropdown pilih varian --}}
            <select name="varian_produk_id"
                class="w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm bg-white
                       focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 focus:outline-none
                       @error('varian_produk_id') border-red-400 @enderror">
                <option value="">— Pilih varian produk —</option>
                @foreach ($varianProduks as $varian)
                    <option value="{{ $varian->id }}" @selected(old('varian_produk_id') == $varian->id)>
                        {{ $varian->produk->nama }} —
                        {{ $varian->nama_varian }}{{ $varian->ukuran ? ' (' . $varian->ukuran . ')' : '' }}
                    </option>
                @endforeach
            </select>
            @error('varian_produk_id')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        @endif
    </div>

    {{-- Dynamic bahan baku rows --}}
    <div class="space-y-3">
        <div class="flex items-center justify-between">
            <label class="text-sm font-medium text-slate-700">
                Bahan Baku <span class="text-red-500">*</span>
            </label>
            <button type="button" @click="addItem()"
                class="text-xs font-medium text-emerald-600 hover:text-emerald-700 transition">
                + Tambah Bahan
            </button>
        </div>

        <div class="text-xs text-slate-400 mb-1">
            Jumlah bahan yang dibutuhkan per 1 unit varian produk terjual
        </div>

        <template x-for="(item, index) in items" :key="index">
            <div class="flex gap-3 items-center">

                {{-- Pilih bahan baku --}}
                <div class="flex-1">
                    <select :name="'items[' + index + '][bahan_baku_id]'"
                        x-model="item.bahan_baku_id"
                        class="w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm bg-white
                               focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 focus:outline-none">
                        <option value="">— Pilih bahan baku —</option>
                        @foreach ($bahanBakus as $bb)
                            <option value="{{ $bb->id }}">{{ $bb->nama }} ({{ $bb->satuan }})</option>
                        @endforeach
                    </select>
                </div>

                {{-- Jumlah per satuan --}}
                <div class="w-36 relative">
                    <input type="number"
                        :name="'items[' + index + '][jumlah_per_satuan]'"
                        x-model="item.jumlah_per_satuan"
                        step="0.0001" min="0.0001"
                        placeholder="0.0000"
                        class="w-full rounded-lg border border-slate-200 px-3 py-2.5 pr-10 text-sm
                               focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 focus:outline-none">
                    <span class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-xs text-slate-400"
                        x-text="getSatuan(item.bahan_baku_id)"></span>
                </div>

                {{-- Tombol hapus baris --}}
                <button type="button" @click="removeItem(index)"
                    x-show="items.length > 1"
                    class="text-slate-300 hover:text-red-500 transition text-lg leading-none">
                    ✕
                </button>
            </div>
        </template>
    </div>

</div>

<div class="mt-8 flex items-center justify-end gap-3 border-t border-slate-100 pt-6">
    <a href="{{ route('app.konversi-produk.index') }}"
        class="px-4 py-2 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-100 transition">
        Batal
    </a>
    <button type="submit"
        class="px-5 py-2 rounded-lg bg-emerald-500 hover:bg-emerald-600 text-white text-sm font-medium transition">
        {{ $submitLabel ?? 'Simpan' }}
    </button>
</div>
