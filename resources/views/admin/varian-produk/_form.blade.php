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

<div class="space-y-6">

    {{-- Produk --}}
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1.5">
            Produk <span class="text-red-500">*</span>
        </label>
        <select name="produk_id"
                class="w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm bg-white
                       focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 focus:outline-none
                       @error('produk_id') border-red-400 @enderror">
            <option value="">— Pilih produk —</option>
            @foreach ($produks as $p)
                <option value="{{ $p->id }}" @selected(old('produk_id', $varian->produk_id) == $p->id)>
                    {{ $p->nama }}
                </option>
            @endforeach
        </select>
        @error('produk_id')
            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
        @enderror
    </div>

    {{-- Nama Varian + Ukuran --}}
    <div class="grid gap-5 md:grid-cols-2">
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">
                Nama Varian <span class="text-red-500">*</span>
            </label>
            <input type="text" name="nama_varian"
                   value="{{ old('nama_varian', $varian->nama_varian) }}"
                   placeholder="Contoh: Original, Pandan, Cokelat"
                   class="w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm
                          focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 focus:outline-none
                          @error('nama_varian') border-red-400 @enderror">
            @error('nama_varian')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">
                Ukuran <span class="text-xs text-slate-400 font-normal">(opsional)</span>
            </label>
            <input type="text" name="ukuran"
                   value="{{ old('ukuran', $varian->ukuran) }}"
                   placeholder="Contoh: 250ml, 500ml"
                   class="w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm
                          focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 focus:outline-none">
        </div>
    </div>

    {{-- Harga + Status --}}
    <div class="grid gap-5 md:grid-cols-2">
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">
                Harga <span class="text-red-500">*</span>
            </label>
            <div class="relative">
                <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-sm text-slate-400 font-medium">Rp</span>
                <input type="number" name="harga"
                       value="{{ old('harga', $varian->harga) }}"
                       step="1" min="0" placeholder="0"
                       class="w-full rounded-lg border border-slate-200 pl-10 pr-3 py-2.5 text-sm
                              focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 focus:outline-none
                              @error('harga') border-red-400 @enderror">
            </div>
            @error('harga')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex items-end pb-0.5">
            <label class="inline-flex items-center gap-3 cursor-pointer select-none">
                <input type="hidden" name="is_active" value="0">
                <div class="relative">
                    <input type="checkbox" name="is_active" value="1" id="is_active"
                           @checked(old('is_active', $varian->is_active ?? true))
                           class="sr-only peer">
                    <div class="w-11 h-6 rounded-full bg-slate-200 peer-checked:bg-emerald-500 transition-colors"></div>
                    <div class="absolute top-0.5 left-0.5 w-5 h-5 rounded-full bg-white shadow
                                transition-transform peer-checked:translate-x-5"></div>
                </div>
                <span class="text-sm font-medium text-slate-700">Aktif</span>
            </label>
        </div>
    </div>

</div>

<div class="mt-8 flex items-center justify-end gap-3 border-t border-slate-100 pt-6">
    <a href="{{ route('app.varian-produk.index') }}"
       class="px-4 py-2 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-100 transition">
        Batal
    </a>
    <button type="submit"
            class="px-5 py-2 rounded-lg bg-emerald-500 hover:bg-emerald-600 text-white text-sm font-medium transition">
        {{ $submitLabel ?? 'Simpan' }}
    </button>
</div>
