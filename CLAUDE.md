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
