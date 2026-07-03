# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

**Segar Alami** is a Laravel 13 inventory and point-of-sale web application for a natural/fresh food business. It manages products, raw material stock, sales transactions, and generates owner reports with PDF export.

## Common Commands

```bash
# Full dev environment (server + queue + log + vite, all concurrent)
composer dev

# First-time setup
composer setup

# Run all tests
composer test

# Run a single test
php artisan test --filter=TestClassName

# Code style (Laravel Pint)
./vendor/bin/pint

# Migrations + seeding
php artisan migrate
php artisan db:seed
```

## Default Seeded Credentials

| Role  | Email                  | Password   |
|-------|------------------------|------------|
| owner | owner@segar.test       | password   |
| admin | admin@segar.test       | password   |

## Architecture

### Role-Based Access (Spatie Permission)

Two roles gate all routes via `role:admin` / `role:owner` middleware defined in `bootstrap/app.php`:

- **admin** — manages day-to-day operations: kategori, produk, varian produk, bahan baku, bahan masuk/keluar, penjualan, safety stock
- **owner** — reads reports (laporan penjualan, laporan persediaan) and manages user accounts

Both roles share the `/app/dashboard` route; `DashboardController::index()` checks the role and returns different views (`admin.dashboard` vs `owner.dashboard`).

### Controller Namespaces

- `App\Http\Controllers\Admin\` — all admin operations
- `App\Http\Controllers\Owner\` — reports and user management
- `App\Http\Controllers\Auth\` — Laravel Breeze authentication stack

### Domain Models

| Model          | Table            | Key Relationships |
|----------------|------------------|-------------------|
| `Produk`       | `produk`         | belongsTo Kategori, hasMany VarianProduk |
| `VarianProduk` | `varian_produk`  | belongsTo Produk |
| `BahanBaku`    | `bahan_baku`     | hasMany BahanMasuk, BahanKeluar |
| `BahanMasuk`   | `bahan_masuk`    | belongsTo BahanBaku (tracks `lead_time_hari`, `tanggal_kadaluarsa`) |
| `BahanKeluar`  | `bahan_keluar`   | belongsTo BahanBaku |
| `Penjualan`    | `penjualan`      | belongsTo User, hasMany DetailPenjualan |
| `DetailPenjualan` | `detail_penjualan` | belongsTo Penjualan, belongsTo VarianProduk |

### Safety Stock / ROP Calculation

`BahanBaku::hitungSsRop()` computes Safety Stock and Reorder Point from historical consumption (BahanKeluar) and lead times (BahanMasuk). Only bahan baku with `tracking_ss_rop = true` appear in the safety stock view. Formula: `SS = (Dmax × Lmax) − (D × L)`, `ROP = (D × L) + SS`.

### View Layer

- Blade layouts: `resources/views/layouts/` (Breeze layouts) and `resources/views/components/layouts/admin.blade.php` (admin shell)
- Reusable UI components in `resources/views/components/admin/` (card, button, input, select, textarea, icon, sidebar, topbar)
- PDF export views in `resources/views/owner/pdf/` rendered via barryvdh/laravel-dompdf

### Global View Data

`AppServiceProvider::boot()` registers a View composer on `components.layouts.admin` that injects `$expiredCount` (bahan masuk expiring within 7 days or already expired) for the admin sidebar badge — only when the authenticated user has the `admin` role.

### Frontend Stack

Tailwind CSS + Alpine.js + Vite. Run `npm run dev` for hot reload (included in `composer dev`). Asset entry points are `resources/css/app.css` and `resources/js/app.js`.

### API Endpoint

`GET /app/api/varian-harga/{varianProduk}` — returns `harga`, `nama_varian`, `ukuran` as JSON. Used in the POS create-sale form to populate price on variant selection.

---

## System Overview

### Ringkasan Sistem

**Segar Alami** adalah aplikasi web manajemen inventori dan point-of-sale berbasis Laravel 13 untuk bisnis makanan segar/alami. Sistem ini mencakup manajemen produk berjenjang, pelacakan stok bahan baku dengan tanggal kadaluarsa, pencatatan transaksi penjualan, kalkulasi safety stock/ROP, dan ekspor laporan PDF.

- **Framework:** Laravel 13, PHP 8.4+
- **Database:** SQLite
- **Frontend:** Tailwind CSS + Alpine.js + Vite
- **Auth:** Laravel Breeze (session-based, email verification)
- **Roles:** Spatie Laravel Permission (`admin`, `owner`)

---

### Role User

| Role  | Akses |
|-------|-------|
| **admin** | CRUD kategori, produk, varian, bahan baku, bahan masuk/keluar, penjualan, konversi produk; melihat safety stock & masa simpan |
| **owner** | Membaca laporan penjualan & persediaan, ekspor PDF, manajemen akun user |

Kedua role berbagi `/app/dashboard` — `DashboardController::index()` mengarahkan ke view yang berbeda berdasarkan role.

---

### Fitur Utama

#### 1. Manajemen Produk
- **Kategori** — nama, urutan tampil, status aktif
- **Produk** — nama, foto (storage publik), deskripsi, kategori, status aktif
- **Varian Produk** — ukuran, harga, status aktif per varian
- **Konversi Produk (BOM)** — resep bahan baku per varian: `jumlah_per_satuan` dari setiap `bahan_baku` untuk satu satuan varian

#### 2. Manajemen Bahan Baku & Stok
- **Bahan Baku** — nama, satuan, stok saat ini, stok minimum, harga per satuan, kategori (utama/pendukung), flag `tracking_ss_rop`
- **Bahan Masuk** — penambahan stok: otomatis increment `stok_saat_ini`, catat `lead_time_hari`, `tanggal_kadaluarsa`, nama supplier
- **Bahan Keluar** — pengurangan stok: validasi cukup stok, otomatis decrement `stok_saat_ini`; delete mengembalikan stok

#### 3. Penjualan (POS)
- Buat transaksi `penjualan` + baris `detail_penjualan` (varian + jumlah + harga)
- Halaman show menampilkan estimasi konsumsi bahan baku berdasarkan konversi produk
- Filter list berdasarkan rentang tanggal

#### 4. Safety Stock & ROP
- Hanya bahan baku dengan `tracking_ss_rop = true`
- Kalkulasi dari histori `bahan_keluar` (konsumsi) dan `bahan_masuk` (lead time):
  - `D` = rata-rata pemakaian harian, `Dmax` = pemakaian harian tertinggi
  - `L` = rata-rata lead time, `Lmax` = lead time terpanjang
  - `SS = (Dmax × Lmax) − (D × L)`
  - `ROP = (D × L) + SS`

#### 5. Masa Simpan (Shelf Life)
- Mengelompokkan `bahan_masuk` berdasarkan status kadaluarsa: **kadaluarsa** / **mendekati** (≤7 hari) / **aman**
- Badge di sidebar admin menampilkan `expiredCount` (kadaluarsa atau mendekati kadaluarsa)

#### 6. Laporan (Owner)
- **Laporan Penjualan** — rekapitulasi bulanan, tren harian, top 10 produk per volume & pendapatan
- **Laporan Persediaan** — status stok (aman/kritis), nilai total inventori
- Ekspor PDF via `barryvdh/laravel-dompdf`

---

### Struktur Database (13 Tabel)

#### Autentikasi & Sesi
| Tabel | Kolom Utama |
|-------|-------------|
| `users` | id, name, email, password, email_verified_at |
| `sessions` | id, user_id, ip_address, payload, last_activity |
| `password_reset_tokens` | email, token, created_at |

#### Roles & Permission (Spatie)
| Tabel | Fungsi |
|-------|--------|
| `roles` | nama role + guard_name |
| `permissions` | nama permission + guard_name |
| `model_has_roles` | pivot user ↔ role |
| `model_has_permissions` | pivot user ↔ permission |
| `role_has_permissions` | pivot role ↔ permission |

#### Domain Bisnis
| Tabel | Kolom Kunci | Relasi |
|-------|-------------|--------|
| `kategori` | id, nama, urutan, is_active | → produk (1:M) |
| `produk` | id, kategori_id, nama, foto, is_active | kategori (M:1), → varian_produk (1:M) |
| `varian_produk` | id, produk_id, nama_varian, ukuran, harga, is_active | produk (M:1), → konversi_produk (1:M), → detail_penjualan (1:M) |
| `bahan_baku` | id, nama, satuan, stok_saat_ini, stok_minimum, harga_per_satuan, kategori_bb, tracking_ss_rop, is_active | → bahan_masuk, bahan_keluar, konversi_produk (1:M) |
| `bahan_masuk` | id, bahan_baku_id, user_id, tanggal, jumlah, lead_time_hari, tanggal_kadaluarsa, nama_supplier | bahan_baku (M:1) |
| `bahan_keluar` | id, bahan_baku_id, user_id, tanggal, jumlah, keterangan | bahan_baku (M:1) |
| `konversi_produk` | id, varian_produk_id, bahan_baku_id, jumlah_per_satuan | varian_produk (M:1), bahan_baku (M:1); unique constraint pada (varian_produk_id, bahan_baku_id) |
| `penjualan` | id, user_id, tanggal, keterangan, total | → detail_penjualan (1:M) |
| `detail_penjualan` | id, penjualan_id, varian_produk_id, jumlah, harga_satuan, sub_total | penjualan (M:1), varian_produk (M:1) |

---

### Flow Data

```
ADMIN
├─ Produk & Varian → Konversi Produk (BOM: varian × bahan_baku)
│
├─ Bahan Masuk   → +stok_saat_ini (bahan_baku)
│  (lead time, kadaluarsa, supplier)
│
├─ Bahan Keluar  → -stok_saat_ini (validasi cukup stok)
│
└─ Penjualan
   ├─ Buat header (penjualan: user, tanggal, total)
   └─ Buat baris (detail_penjualan: varian, jumlah, harga_satuan, sub_total)
       └─ Show: estimasi konsumsi = detail.jumlah × konversi_produk.jumlah_per_satuan

OWNER
├─ Laporan Penjualan  ← agregat penjualan + detail_penjualan per bulan/kategori
├─ Laporan Persediaan ← snapshot bahan_baku (stok, nilai, status)
└─ Manajemen User     → assign role admin/owner

SISTEM OTOMATIS
└─ AppServiceProvider → View composer → $expiredCount (bahan_masuk kadaluarsa/≤7 hari)
   dikirim ke semua view admin layout
```

---

### Struktur Direktori Kode

```
app/
├─ Http/Controllers/
│  ├─ Admin/          # KategoriController, ProdukController, VarianProdukController,
│  │                  # BahanBakuController, BahanMasukController, BahanKeluarController,
│  │                  # PenjualanController, KonversiProdukController,
│  │                  # SafetyStockController, MasaSimpanController, DashboardController
│  ├─ Owner/          # LaporanPenjualanController, LaporanPersediaanController,
│  │                  # UserManagementController
│  └─ Auth/           # Breeze auth controllers
├─ Http/Requests/Admin/  # Form request validation classes
├─ Models/            # User, Kategori, Produk, VarianProduk, BahanBaku,
│                     # BahanMasuk, BahanKeluar, KonversiProduk, Penjualan, DetailPenjualan
└─ Providers/AppServiceProvider.php  # View composer untuk $expiredCount

resources/views/
├─ admin/             # CRUD views per fitur + analytics (safety-stock, masa-simpan)
├─ owner/             # Laporan views + PDF templates
├─ components/
│  ├─ layouts/admin.blade.php   # Admin shell layout
│  └─ admin/                    # Reusable form components (button, input, select, textarea)
├─ layouts/           # Breeze layouts (app, guest, navigation)
└─ auth/              # Login, forgot-password, reset, verify

routes/
├─ web.php            # Semua route (admin prefix /app/, owner prefix /app/)
└─ auth.php           # Breeze auth routes
```

