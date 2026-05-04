@props(['kategori' => null])

<div class="space-y-5">
    <x-admin.input
        label="Nama Kategori"
        name="nama"
        :value="$kategori?->nama"
        required
        placeholder="Misal: Susu Kedelai" />

    <x-admin.input
        label="Urutan Tampil"
        name="urutan"
        type="number"
        :value="$kategori?->urutan ?? 0"
        required
        placeholder="0 = paling atas" />

    <div class="flex items-center gap-2">
        <input type="hidden" name="is_active" value="0">
        <input type="checkbox"
               name="is_active"
               id="is_active"
               value="1"
               @checked(old('is_active', $kategori?->is_active ?? true))
               class="rounded border-slate-300 text-primary-500 focus:ring-primary-500">
        <label for="is_active" class="text-sm font-medium text-slate-700">
            Tampilkan kategori ini (aktif)
        </label>
    </div>
</div>

<div class="flex items-center gap-3 mt-6 pt-6 border-t border-slate-100">
    <x-admin.button type="submit" variant="primary">
        {{ $kategori ? 'Simpan Perubahan' : 'Tambah Kategori' }}
    </x-admin.button>
    <x-admin.button :href="route('app.kategori.index')" variant="secondary">Batal</x-admin.button>
</div>
