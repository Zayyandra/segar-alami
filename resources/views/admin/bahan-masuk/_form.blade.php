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
                <option value="{{ $bb->id }}" @selected(old('bahan_baku_id', $bahanMasuk->bahan_baku_id) == $bb->id)>
                    {{ $bb->nama }} (stok: {{ number_format($bb->stok_saat_ini, 0) }} {{ $bb->satuan }})
                </option>
            @endforeach
        </select>
        @error('bahan_baku_id')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>

    <div class="grid gap-5 md:grid-cols-2">
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">
                Tanggal Terima <span class="text-red-500">*</span>
            </label>
            <input type="date" name="tanggal"
                value="{{ old('tanggal', $bahanMasuk->tanggal?->format('Y-m-d') ?? date('Y-m-d')) }}"
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
                value="{{ old('jumlah', $bahanMasuk->jumlah) }}" placeholder="0"
                class="w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm
                       focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 focus:outline-none
                       @error('jumlah') border-red-400 @enderror">
            @error('jumlah')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>
    </div>

    <div class="grid gap-5 md:grid-cols-2">
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Lead Time (hari)</label>
            <input type="number" name="lead_time_hari" min="0"
                value="{{ old('lead_time_hari', $bahanMasuk->lead_time_hari) }}" placeholder="Contoh: 2"
                class="w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm
                       focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 focus:outline-none">
            <p class="mt-1 text-xs text-slate-400">Berapa hari dari pesan ke barang diterima</p>
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Nama Supplier</label>
            <input type="text" name="nama_supplier"
                value="{{ old('nama_supplier', $bahanMasuk->nama_supplier) }}" placeholder="Contoh: UD Oyon"
                class="w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm
                       focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 focus:outline-none">
        </div>
    </div>

    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1.5">Tanggal Kadaluarsa</label>
        <input type="date" name="tanggal_kadaluarsa"
            value="{{ old('tanggal_kadaluarsa', $bahanMasuk->tanggal_kadaluarsa?->format('Y-m-d')) }}"
            class="w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm
                   focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 focus:outline-none
                   @error('tanggal_kadaluarsa') border-red-400 @enderror">
        <p class="mt-1 text-xs text-slate-400">Kosongkan jika tidak ada masa kadaluarsa</p>
        @error('tanggal_kadaluarsa')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1.5">Keterangan</label>
        <textarea name="keterangan" rows="3" placeholder="Catatan tambahan (opsional)"
            class="w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm
                   focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 focus:outline-none">{{ old('keterangan', $bahanMasuk->keterangan) }}</textarea>
    </div>

</div>

<div class="mt-8 flex items-center justify-end gap-3 border-t border-slate-100 pt-6">
    <a href="{{ route('app.bahan-masuk.index') }}"
        class="px-4 py-2 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-100 transition">Batal</a>
    <button type="submit"
        class="px-5 py-2 rounded-lg bg-emerald-500 hover:bg-emerald-600 text-white text-sm font-medium transition">
        {{ $submitLabel ?? 'Simpan' }}
    </button>
</div>
