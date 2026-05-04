@csrf

<div class="space-y-5">

    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1.5">
            Bahan Baku <span class="text-red-500">*</span>
        </label>
        <select name="bahan_baku_id"
            class="w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm bg-white
                   focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 focus:outline-none
                   @error('bahan_baku_id') border-red-400 @enderror">
            <option value="">— Pilih bahan baku —</option>
            @foreach ($bahanBakus as $bb)
                <option value="{{ $bb->id }}" @selected(old('bahan_baku_id', $bahanKeluar->bahan_baku_id) == $bb->id)>
                    {{ $bb->nama }} (stok: {{ number_format($bb->stok_saat_ini, 0) }} {{ $bb->satuan }})
                </option>
            @endforeach
        </select>
        @error('bahan_baku_id')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>

    <div class="grid gap-5 md:grid-cols-2">
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">
                Tanggal <span class="text-red-500">*</span>
            </label>
            <input type="date" name="tanggal"
                value="{{ old('tanggal', $bahanKeluar->tanggal?->format('Y-m-d') ?? date('Y-m-d')) }}"
                class="w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm
                       focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 focus:outline-none
                       @error('tanggal') border-red-400 @enderror">
            @error('tanggal')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">
                Jumlah <span class="text-red-500">*</span>
            </label>
            <input type="number" name="jumlah" step="0.01" min="0.01"
                value="{{ old('jumlah', $bahanKeluar->jumlah) }}" placeholder="0"
                class="w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm
                       focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 focus:outline-none
                       @error('jumlah') border-red-400 @enderror">
            @error('jumlah')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>
    </div>

    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1.5">Keterangan</label>
        <textarea name="keterangan" rows="3"
            placeholder="Contoh: Produksi 100 botol susu kedelai"
            class="w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm
                   focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 focus:outline-none">{{ old('keterangan', $bahanKeluar->keterangan) }}</textarea>
    </div>

</div>

<div class="mt-8 flex items-center justify-end gap-3 border-t border-slate-100 pt-6">
    <a href="{{ route('app.bahan-keluar.index') }}"
        class="px-4 py-2 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-100 transition">Batal</a>
    <button type="submit"
        class="px-5 py-2 rounded-lg bg-emerald-500 hover:bg-emerald-600 text-white text-sm font-medium transition">
        {{ $submitLabel ?? 'Simpan' }}
    </button>
</div>
