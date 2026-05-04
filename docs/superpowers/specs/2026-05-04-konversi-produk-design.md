# Design: Fitur CRUD Konversi Produk ke Bahan Baku

**Date:** 2026-05-04  
**Project:** Segar Alami (Laravel 13)  
**Scope:** Admin-only CRUD untuk mengelola rasio kebutuhan bahan baku per satuan varian produk terjual.

---

## Context

Setiap varian produk membutuhkan sejumlah bahan baku untuk diproduksi. Data konversi ini menjadi dasar kalkulasi estimasi pemakaian bahan baku otomatis saat penjualan dicatat.

Contoh: 1 botol Susu Kedelai (250ml) membutuhkan 0.1 kg Kacang Kedelai dan 0.02 kg Gula Pasir.

---

## Database

### Tabel: `konversi_produk`

| Kolom              | Tipe            | Constraint                     |
|--------------------|-----------------|--------------------------------|
| id                 | bigIncrements   | PK                             |
| varian_produk_id   | unsignedBigInt  | FK → varian_produk.id          |
| bahan_baku_id      | unsignedBigInt  | FK → bahan_baku.id             |
| jumlah_per_satuan  | decimal(8, 4)   | NOT NULL, > 0                  |
| timestamps         | —               | created_at, updated_at         |

**Unique constraint:** `(varian_produk_id, bahan_baku_id)` — satu kombinasi tidak boleh duplikat.

---

## Models

### `App\Models\KonversiProduk`
- `$table = 'konversi_produk'`
- `$fillable`: `varian_produk_id`, `bahan_baku_id`, `jumlah_per_satuan`
- `$casts`: `jumlah_per_satuan` → `decimal:4`
- Relasi: `belongsTo(VarianProduk::class)`, `belongsTo(BahanBaku::class)`

### Tambahan pada model yang sudah ada
- `VarianProduk::konversiProduk()` → `hasMany(KonversiProduk::class)`
- `BahanBaku::konversiProduk()` → `hasMany(KonversiProduk::class)`

---

## FormRequest

### `App\Http\Requests\Admin\StoreKonversiProdukRequest`
```
varian_produk_id  required | exists:varian_produk,id
bahan_baku_id     required | exists:bahan_baku,id
jumlah_per_satuan required | numeric | min:0.0001
+ unique(['konversi_produk'], ['varian_produk_id', 'bahan_baku_id'])
```

### `App\Http\Requests\Admin\UpdateKonversiProdukRequest`
Sama dengan Store, tetapi unique rule mengabaikan ID record saat ini.

---

## Controller

`App\Http\Controllers\Admin\KonversiProdukController`

| Method    | Route                             | Action                                        |
|-----------|-----------------------------------|-----------------------------------------------|
| index     | GET /app/konversi-produk          | List dengan filter by varian_produk_id        |
| create    | GET /app/konversi-produk/create   | Form tambah, inject semua VarianProduk + BB   |
| store     | POST /app/konversi-produk         | Validate + create, redirect ke index          |
| edit      | GET /app/konversi-produk/{id}/edit| Form edit, inject semua VarianProduk + BB     |
| update    | PUT /app/konversi-produk/{id}     | Validate + update, redirect ke index          |
| destroy   | DELETE /app/konversi-produk/{id}  | Delete, redirect ke index                     |

Route parameter: `konversiProduk` (camelCase, Laravel convention).

---

## Routes

Ditambahkan di grup `role:admin` yang sudah ada di `routes/web.php`:

```php
Route::resource('konversi-produk', KonversiProdukController::class)
    ->parameters(['konversi-produk' => 'konversiProduk'])
    ->except(['show']);
```

---

## Views

Lokasi: `resources/views/admin/konversi-produk/`

### `index.blade.php`
- Layout: `<x-layouts.admin title="Konversi Produk">`
- Tabel kolom: Varian Produk (+ nama produk subtitle), Bahan Baku (+ satuan), Jumlah per Satuan, Aksi
- Filter: `<select>` untuk filter by varian_produk_id (optional, submitted via GET)
- Tombol "Tambah Konversi" → `app.konversi-produk.create`
- Flash: `session('success')`, `session('error')`
- Empty state dengan link ke create
- Pagination

### `create.blade.php`
- Layout: `<x-layouts.admin title="Tambah Konversi Produk">`
- Card dengan `<form method="POST">` → `@include('admin.konversi-produk._form')`

### `edit.blade.php`
- Layout: `<x-layouts.admin title="Edit Konversi Produk">`
- Card dengan `<form method="POST">` + `@method('PUT')` → `@include('admin.konversi-produk._form')`

### `_form.blade.php`
Tiga field:
1. **Varian Produk** — `<select name="varian_produk_id">` diisi `VarianProduk::with('produk')->get()`, label: "Nama Produk — Nama Varian (Ukuran)"
2. **Bahan Baku** — `<select name="bahan_baku_id" x-model="selectedBB" @change="updateSatuan()">` diisi `BahanBaku::orderBy('nama')->get()`, label: "Nama (satuan)"
3. **Jumlah per Satuan** — `<input type="number" step="0.0001" min="0.0001">` dengan suffix satuan bahan baku yang dipilih (Alpine.js reactive)

Style: konsisten dengan `_form.blade.php` yang lain — `rounded-lg border border-slate-200`, `focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20`.

Submit button: `bg-emerald-500 hover:bg-emerald-600`.

---

## Sidebar

Grup **Katalog** di `resources/views/components/admin/sidebar.blade.php`, setelah entry "Varian Produk":

```php
['route' => 'app.konversi-produk.index', 'label' => 'Konversi Produk', 'icon' => 'arrows-right-left'],
```

Icon `arrows-right-left` (HeroIcons outline) ditambahkan ke `resources/views/components/admin/icon.blade.php`.

---

## Validation Summary

| Field              | Rules                                                        |
|--------------------|--------------------------------------------------------------|
| varian_produk_id   | required, exists:varian_produk,id                            |
| bahan_baku_id      | required, exists:bahan_baku,id                               |
| jumlah_per_satuan  | required, numeric, min:0.0001                                |
| (pair)             | unique(konversi_produk, [varian_produk_id, bahan_baku_id])   |

Pair uniqueness: `Rule::unique('konversi_produk')->where('bahan_baku_id', $this->bahan_baku_id)` — on update, add `->ignore($this->route('konversiProduk'))`.

---

## Out of Scope

- Kalkulasi otomatis pengurangan stok saat penjualan disimpan (fitur terpisah)
- Import/export bulk konversi
- History perubahan rasio
