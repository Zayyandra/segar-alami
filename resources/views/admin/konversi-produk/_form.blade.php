{{-- resources/views/admin/konversi-produk/_form.blade.php --}}
@csrf

@if ($errors->any())
    <div class="mb-6 rounded-lg border border-red-100 bg-red-50 px-4 py-3 text-sm text-red-700">
        <p class="font-medium mb-1">Terdapat kesalahan input:</p>
        <ul class="list-disc list-inside space-y-0.5">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="space-y-5"
     x-data="{
         selectedBB: '{{ old('bahan_baku_id', $konversiProduk->bahan_baku_id ?? '') }}',
         satuanMap: {{ Js::from($bahanBakus->pluck('satuan', 'id')) }},
         get satuan() { return this.satuanMap[this.selectedBB] ?? '—' }
     }">

    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1.5">
            Varian Produk <span class="text-red-500">*</span>
        </label>
        <select name="varian_produk_id"
                class="w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm bg-white
                       focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 focus:outline-none
                       @error('varian_produk_id') border-red-400 @enderror">
            <option value="">— Pilih varian produk —</option>
            @foreach ($varianProduks as $varian)
                <option value="{{ $varian->id }}"
                        @selected(old('varian_produk_id', $konversiProduk->varian_produk_id) == $varian->id)>
                    {{ $varian->produk->nama }} — {{ $varian->nama_varian }}{{ $varian->ukuran ? ' (' . $varian->ukuran . ')' : '' }}
                </option>
            @endforeach
        </select>
        @error('varian_produk_id')
            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1.5">
            Bahan Baku <span class="text-red-500">*</span>
        </label>
        <select name="bahan_baku_id" x-model="selectedBB"
                class="w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm bg-white
                       focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 focus:outline-none
                       @error('bahan_baku_id') border-red-400 @enderror">
            <option value="">— Pilih bahan baku —</option>
            @foreach ($bahanBakus as $bb)
                <option value="{{ $bb->id }}"
                        @selected(old('bahan_baku_id', $konversiProduk->bahan_baku_id) == $bb->id)>
                    {{ $bb->nama }} ({{ $bb->satuan }})
                </option>
            @endforeach
        </select>
        @error('bahan_baku_id')
            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1.5">
            Jumlah per Satuan <span class="text-red-500">*</span>
        </label>
        <div class="relative">
            <input type="number" name="jumlah_per_satuan" step="0.0001" min="0.0001"
                   value="{{ old('jumlah_per_satuan', $konversiProduk->jumlah_per_satuan ?? '') }}"
                   placeholder="Contoh: 0.1"
                   class="w-full rounded-lg border border-slate-200 px-3 py-2.5 pr-20 text-sm
                          focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 focus:outline-none
                          @error('jumlah_per_satuan') border-red-400 @enderror">
            <span class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-xs text-slate-400"
                  x-text="satuan">—</span>
        </div>
        <p class="mt-1 text-xs text-slate-400">
            Jumlah bahan baku yang dibutuhkan per 1 unit varian produk terjual
        </p>
        @error('jumlah_per_satuan')
            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
        @enderror
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
