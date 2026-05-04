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

<div class="space-y-5">

    <div class="grid gap-5 md:grid-cols-2">
        <div class="md:col-span-2">
            <label class="block text-sm font-medium text-slate-700 mb-1.5">
                Nama Bahan Baku <span class="text-red-500">*</span>
            </label>
            <input type="text" name="nama" value="{{ old('nama', $bahanBaku->nama) }}"
                placeholder="Contoh: Kedelai, Gula Pasir"
                class="w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm
                       focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 focus:outline-none
                       @error('nama') border-red-400 @enderror">
            @error('nama')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">
                Satuan <span class="text-red-500">*</span>
            </label>
            <input type="text" name="satuan" value="{{ old('satuan', $bahanBaku->satuan) }}"
                placeholder="Contoh: kg, gram, ml, buah"
                class="w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm
                       focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 focus:outline-none
                       @error('satuan') border-red-400 @enderror">
            @error('satuan')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">
                Kategori <span class="text-red-500">*</span>
            </label>
            <select name="kategori_bb"
                class="w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm bg-white
                       focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 focus:outline-none
                       @error('kategori_bb') border-red-400 @enderror">
                <option value="">— Pilih kategori —</option>
                <option value="utama" @selected(old('kategori_bb', $bahanBaku->kategori_bb) === 'utama')>Utama</option>
                <option value="pendukung" @selected(old('kategori_bb', $bahanBaku->kategori_bb) === 'pendukung')>Pendukung</option>
            </select>
            @error('kategori_bb')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <div class="rounded-xl border border-slate-200 bg-slate-50 px-5 py-4">
        <h3 class="text-sm font-semibold text-slate-700 mb-3">Data Persediaan</h3>
        <div class="grid gap-4 md:grid-cols-3">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Stok Saat Ini</label>
                <div class="relative">
                    <input type="number" name="stok_saat_ini"
                        value="{{ old('stok_saat_ini', $bahanBaku->stok_saat_ini ?? 0) }}" min="0" step="0.01"
                        placeholder="0"
                        class="w-full rounded-lg border border-slate-200 px-3 py-2.5 pr-16 text-sm
                               focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 focus:outline-none">
                    <span class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-xs text-slate-400">
                        {{ $bahanBaku->satuan ?? 'satuan' }}
                    </span>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Stok Minimum</label>
                <div class="relative">
                    <input type="number" name="stok_minimum"
                        value="{{ old('stok_minimum', $bahanBaku->stok_minimum ?? 0) }}" min="0" step="0.01"
                        placeholder="0"
                        class="w-full rounded-lg border border-slate-200 px-3 py-2.5 pr-16 text-sm
                               focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 focus:outline-none">
                    <span class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-xs text-slate-400">
                        {{ $bahanBaku->satuan ?? 'satuan' }}
                    </span>
                </div>
                <p class="mt-1 text-xs text-slate-400">Batas minimum sebelum restock</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Harga per Satuan</label>
                <div class="relative">
                    <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-sm text-slate-400">Rp</span>
                    <input type="number" name="harga_per_satuan"
                        value="{{ old('harga_per_satuan', $bahanBaku->harga_per_satuan ?? 0) }}" min="0" step="1"
                        placeholder="0"
                        class="w-full rounded-lg border border-slate-200 pl-9 pr-3 py-2.5 text-sm
                               focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 focus:outline-none">
                </div>
            </div>
        </div>
    </div>

    <div class="rounded-lg border border-slate-200 bg-slate-50 px-4 py-4">
        <div class="flex items-start gap-3">
            <input type="hidden" name="tracking_ss_rop" value="0">
            <input type="checkbox" name="tracking_ss_rop" id="tracking_ss_rop" value="1"
                @checked(old('tracking_ss_rop', $bahanBaku->tracking_ss_rop))
                class="mt-0.5 h-4 w-4 rounded border-slate-300 text-emerald-600">
            <div>
                <label for="tracking_ss_rop" class="text-sm font-medium text-slate-700 cursor-pointer">
                    Aktifkan Tracking Safety Stock & ROP
                </label>
                <p class="text-xs text-slate-400 mt-0.5">
                    Sistem akan menghitung Safety Stock dan Reorder Point otomatis dari riwayat pemakaian dan pengadaan bahan baku ini.
                </p>
            </div>
        </div>
    </div>

    <div class="flex items-center gap-3">
        <input type="hidden" name="is_active" value="0">
        <div class="relative">
            <input type="checkbox" name="is_active" value="1" id="is_active"
                @checked(old('is_active', $bahanBaku->is_active ?? true))
                class="sr-only peer">
            <div class="w-11 h-6 rounded-full bg-slate-200 peer-checked:bg-emerald-500 transition-colors cursor-pointer"
                onclick="document.getElementById('is_active').click()"></div>
            <div class="absolute top-0.5 left-0.5 w-5 h-5 rounded-full bg-white shadow
                        transition-transform peer-checked:translate-x-5 pointer-events-none"></div>
        </div>
        <label class="text-sm font-medium text-slate-700 cursor-pointer"
            onclick="document.getElementById('is_active').click()">Aktif</label>
    </div>

</div>

<div class="mt-8 flex items-center justify-end gap-3 border-t border-slate-100 pt-6">
    <a href="{{ route('app.bahan-baku.index') }}"
        class="px-4 py-2 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-100 transition">
        Batal
    </a>
    <button type="submit"
        class="px-5 py-2 rounded-lg bg-emerald-500 hover:bg-emerald-600 text-white text-sm font-medium transition">
        {{ $submitLabel ?? 'Simpan' }}
    </button>
</div>
