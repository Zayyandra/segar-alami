@props(['produk' => null, 'kategori' => collect()])

<div class="space-y-5">
    <x-admin.select
        label="Kategori"
        name="kategori_id"
        :options="$kategori"
        :value="$produk?->kategori_id"
        required
        placeholder="-- Pilih kategori --" />

    <x-admin.input
        label="Nama Produk"
        name="nama"
        :value="$produk?->nama"
        required
        placeholder="Misal: Susu Kedelai" />

    <x-admin.textarea
        label="Deskripsi"
        name="deskripsi"
        :value="$produk?->deskripsi"
        rows="3"
        placeholder="Deskripsi singkat produk (opsional)" />

    {{-- Foto upload --}}
    <div>
        <label for="foto" class="block text-sm font-medium text-slate-700 mb-1.5">Foto Produk</label>

        @if ($produk?->foto)
            <div class="mb-3">
                <img src="{{ $produk->foto_url }}" alt="{{ $produk->nama }}"
                     class="w-32 h-32 object-cover rounded-lg border border-slate-200">
                <p class="text-xs text-slate-500 mt-1">Foto saat ini. Upload foto baru untuk mengganti.</p>
            </div>
        @endif

        <input type="file" name="foto" id="foto" accept="image/jpeg,image/png,image/webp"
               class="block w-full text-sm text-slate-600 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100 cursor-pointer">
        <p class="mt-1.5 text-xs text-slate-500">JPG, PNG, atau WebP. Maks 2MB.</p>

        @error('foto')<p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>

    {{-- Toggle aktif --}}
    <div class="flex items-center gap-2">
        <input type="hidden" name="is_active" value="0">
        <input type="checkbox" name="is_active" id="is_active" value="1"
               @checked(old('is_active', $produk?->is_active ?? true))
               class="rounded border-slate-300 text-primary-500 focus:ring-primary-500">
        <label for="is_active" class="text-sm font-medium text-slate-700">
            Tampilkan produk ini (aktif)
        </label>
    </div>
</div>

<div class="flex items-center gap-3 mt-6 pt-6 border-t border-slate-100">
    <x-admin.button type="submit" variant="primary">
        {{ $produk ? 'Simpan Perubahan' : 'Tambah Produk' }}
    </x-admin.button>
    <x-admin.button :href="route('app.produk.index')" variant="secondary">Batal</x-admin.button>
</div>
