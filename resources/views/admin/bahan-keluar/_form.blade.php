@csrf

<div class="space-y-5">

    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1.5">
            Bahan Baku <span class="text-red-500">*</span>
        </label>
        <select name="bahan_baku_id" id="bahan_baku_id"
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
            <input type="number" name="jumlah" id="jumlah" step="0.01" min="0.01"
                value="{{ old('jumlah', $bahanKeluar->jumlah) }}" placeholder="0"
                class="w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm
                       focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 focus:outline-none
                       @error('jumlah') border-red-400 @enderror">
            @error('jumlah')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>
    </div>

    <div class="rounded-xl border border-emerald-100 bg-emerald-50/50 p-4">
        <label class="block text-sm font-medium text-slate-700 mb-1.5">
            Produksi untuk Varian Produk <span class="text-slate-400 font-normal">(opsional)</span>
        </label>
        <p class="text-xs text-slate-500 mb-2">
            Isi hanya jika bahan keluar ini dipakai untuk produksi. Stok varian produk akan bertambah
            otomatis sesuai rasio konversi. Kosongkan jika bahan keluar bukan untuk produksi
            (contoh: rusak, kadaluarsa, dibuang).
        </p>
        <select name="varian_produk_id" id="varian_produk_id"
            class="w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm bg-white
                   focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 focus:outline-none
                   @error('varian_produk_id') border-red-400 @enderror">
            <option value="">— Bukan untuk produksi —</option>
            @foreach ($varianProduk as $vp)
                <option value="{{ $vp->id }}" @selected(old('varian_produk_id', $bahanKeluar->varian_produk_id) == $vp->id)>
                    {{ $vp->produk->nama }} — {{ $vp->nama_varian }} ({{ $vp->ukuran }})
                </option>
            @endforeach
        </select>
        @error('varian_produk_id')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror

        <p id="preview-konversi" class="mt-2 text-xs font-medium text-emerald-700 hidden"></p>
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

<script>
(function () {
    // Data rasio konversi per (bahan_baku_id, varian_produk_id) dikirim dari server
    const konversiMap = @json($konversiMapJs ?? []);

    const bahanSelect  = document.getElementById('bahan_baku_id');
    const varianSelect = document.getElementById('varian_produk_id');
    const jumlahInput  = document.getElementById('jumlah');
    const preview      = document.getElementById('preview-konversi');

    function updatePreview() {
        const bahanId  = bahanSelect.value;
        const varianId = varianSelect.value;
        const jumlah   = parseFloat(jumlahInput.value);

        if (!bahanId || !varianId || !jumlah || jumlah <= 0) {
            preview.classList.add('hidden');
            return;
        }

        const key = bahanId + '_' + varianId;
        const rasio = konversiMap[key];

        if (!rasio) {
            preview.textContent = '⚠ Konversi belum diatur untuk kombinasi ini — stok produk tidak akan bertambah otomatis.';
            preview.classList.remove('hidden', 'text-emerald-700');
            preview.classList.add('text-amber-600');
            return;
        }

        const hasil = Math.floor(jumlah / rasio);
        preview.textContent = `✓ Estimasi hasil produksi: ${hasil} unit (otomatis ditambahkan ke stok varian).`;
        preview.classList.remove('hidden', 'text-amber-600');
        preview.classList.add('text-emerald-700');
    }

    bahanSelect.addEventListener('change', updatePreview);
    varianSelect.addEventListener('change', updatePreview);
    jumlahInput.addEventListener('input', updatePreview);
    updatePreview();
})();
</script>
