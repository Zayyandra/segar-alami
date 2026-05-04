# KonversiProduk CRUD Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Tambah fitur CRUD Konversi Produk ke Bahan Baku untuk mengelola rasio kebutuhan bahan baku per satuan varian produk terjual di admin panel Segar Alami.

**Architecture:** Resource CRUD mengikuti pola yang ada — FormRequest untuk validasi, resource controller di `App\Http\Controllers\Admin\`, route resource di grup `role:admin`, views Blade dengan Tailwind emerald theme. Menu baru ditambahkan di grup Katalog sidebar admin.

**Tech Stack:** Laravel 13, Spatie Permission, Tailwind CSS, Alpine.js, Blade components, PHPUnit.

---

## File Map

| Status | File |
|--------|------|
| CREATE | `database/migrations/2026_05_04_000001_create_konversi_produk_table.php` |
| CREATE | `app/Models/KonversiProduk.php` |
| MODIFY | `app/Models/VarianProduk.php` — tambah `hasMany(KonversiProduk)` |
| MODIFY | `app/Models/BahanBaku.php` — tambah `hasMany(KonversiProduk)` |
| CREATE | `app/Http/Requests/Admin/StoreKonversiProdukRequest.php` |
| CREATE | `app/Http/Requests/Admin/UpdateKonversiProdukRequest.php` |
| CREATE | `tests/Feature/Admin/KonversiProdukTest.php` |
| CREATE | `app/Http/Controllers/Admin/KonversiProdukController.php` |
| MODIFY | `routes/web.php` — tambah resource route + import |
| CREATE | `resources/views/admin/konversi-produk/_form.blade.php` |
| CREATE | `resources/views/admin/konversi-produk/create.blade.php` |
| CREATE | `resources/views/admin/konversi-produk/edit.blade.php` |
| CREATE | `resources/views/admin/konversi-produk/index.blade.php` |
| MODIFY | `resources/views/components/admin/icon.blade.php` — tambah icon `arrows-right-left` |
| MODIFY | `resources/views/components/admin/sidebar.blade.php` — tambah menu item |

---

## Task 1: Migration

**Files:**
- Create: `database/migrations/2026_05_04_000001_create_konversi_produk_table.php`

- [ ] **Step 1.1: Buat file migration**

```php
<?php
// database/migrations/2026_05_04_000001_create_konversi_produk_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('konversi_produk', function (Blueprint $table) {
            $table->id();
            $table->foreignId('varian_produk_id')->constrained('varian_produk')->cascadeOnDelete();
            $table->foreignId('bahan_baku_id')->constrained('bahan_baku')->cascadeOnDelete();
            $table->decimal('jumlah_per_satuan', 8, 4);
            $table->timestamps();
            $table->unique(['varian_produk_id', 'bahan_baku_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('konversi_produk');
    }
};
```

- [ ] **Step 1.2: Jalankan migration**

```bash
php artisan migrate
```

Expected output: `... create_konversi_produk_table ............... DONE`

- [ ] **Step 1.3: Commit**

```bash
git add database/migrations/2026_05_04_000001_create_konversi_produk_table.php
git commit -m "feat: add konversi_produk migration"
```

---

## Task 2: Model KonversiProduk + Update Relasi

**Files:**
- Create: `app/Models/KonversiProduk.php`
- Modify: `app/Models/VarianProduk.php`
- Modify: `app/Models/BahanBaku.php`

- [ ] **Step 2.1: Buat model KonversiProduk**

```php
<?php
// app/Models/KonversiProduk.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KonversiProduk extends Model
{
    protected $table = 'konversi_produk';

    protected $fillable = [
        'varian_produk_id',
        'bahan_baku_id',
        'jumlah_per_satuan',
    ];

    protected $casts = [
        'jumlah_per_satuan' => 'decimal:4',
    ];

    public function varianProduk(): BelongsTo
    {
        return $this->belongsTo(VarianProduk::class);
    }

    public function bahanBaku(): BelongsTo
    {
        return $this->belongsTo(BahanBaku::class);
    }
}
```

- [ ] **Step 2.2: Tambahkan relasi `konversiProduk` di VarianProduk**

Di `app/Models/VarianProduk.php`, tambahkan method berikut setelah method `produk()`:

```php
public function konversiProduk(): \Illuminate\Database\Eloquent\Relations\HasMany
{
    return $this->hasMany(KonversiProduk::class);
}
```

- [ ] **Step 2.3: Tambahkan relasi `konversiProduk` di BahanBaku**

Di `app/Models/BahanBaku.php`, tambahkan method berikut setelah method `bahanKeluar()`:

```php
public function konversiProduk(): \Illuminate\Database\Eloquent\Relations\HasMany
{
    return $this->hasMany(KonversiProduk::class);
}
```

- [ ] **Step 2.4: Commit**

```bash
git add app/Models/KonversiProduk.php app/Models/VarianProduk.php app/Models/BahanBaku.php
git commit -m "feat: add KonversiProduk model and relationships"
```

---

## Task 3: FormRequests

**Files:**
- Create: `app/Http/Requests/Admin/StoreKonversiProdukRequest.php`
- Create: `app/Http/Requests/Admin/UpdateKonversiProdukRequest.php`

- [ ] **Step 3.1: Buat StoreKonversiProdukRequest**

```php
<?php
// app/Http/Requests/Admin/StoreKonversiProdukRequest.php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreKonversiProdukRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasRole('admin') ?? false;
    }

    public function rules(): array
    {
        return [
            'varian_produk_id'  => ['required', 'integer', 'exists:varian_produk,id'],
            'bahan_baku_id'     => [
                'required',
                'integer',
                'exists:bahan_baku,id',
                Rule::unique('konversi_produk', 'bahan_baku_id')
                    ->where('varian_produk_id', $this->varian_produk_id),
            ],
            'jumlah_per_satuan' => ['required', 'numeric', 'min:0.0001'],
        ];
    }

    public function attributes(): array
    {
        return [
            'varian_produk_id'  => 'varian produk',
            'bahan_baku_id'     => 'bahan baku',
            'jumlah_per_satuan' => 'jumlah per satuan',
        ];
    }

    public function messages(): array
    {
        return [
            'bahan_baku_id.unique' => 'Kombinasi varian produk dan bahan baku ini sudah terdaftar.',
        ];
    }
}
```

- [ ] **Step 3.2: Buat UpdateKonversiProdukRequest**

```php
<?php
// app/Http/Requests/Admin/UpdateKonversiProdukRequest.php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateKonversiProdukRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasRole('admin') ?? false;
    }

    public function rules(): array
    {
        return [
            'varian_produk_id'  => ['required', 'integer', 'exists:varian_produk,id'],
            'bahan_baku_id'     => [
                'required',
                'integer',
                'exists:bahan_baku,id',
                Rule::unique('konversi_produk', 'bahan_baku_id')
                    ->where('varian_produk_id', $this->varian_produk_id)
                    ->ignore($this->route('konversiProduk')),
            ],
            'jumlah_per_satuan' => ['required', 'numeric', 'min:0.0001'],
        ];
    }

    public function attributes(): array
    {
        return [
            'varian_produk_id'  => 'varian produk',
            'bahan_baku_id'     => 'bahan baku',
            'jumlah_per_satuan' => 'jumlah per satuan',
        ];
    }

    public function messages(): array
    {
        return [
            'bahan_baku_id.unique' => 'Kombinasi varian produk dan bahan baku ini sudah terdaftar.',
        ];
    }
}
```

- [ ] **Step 3.3: Commit**

```bash
git add app/Http/Requests/Admin/StoreKonversiProdukRequest.php app/Http/Requests/Admin/UpdateKonversiProdukRequest.php
git commit -m "feat: add KonversiProduk FormRequests with pair uniqueness validation"
```

---

## Task 4: Feature Tests (TDD — tulis dulu, jalankan untuk verifikasi fail)

**Files:**
- Create: `tests/Feature/Admin/KonversiProdukTest.php`

- [ ] **Step 4.1: Buat file test**

```php
<?php
// tests/Feature/Admin/KonversiProdukTest.php

namespace Tests\Feature\Admin;

use App\Models\BahanBaku;
use App\Models\Kategori;
use App\Models\KonversiProduk;
use App\Models\Produk;
use App\Models\User;
use App\Models\VarianProduk;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class KonversiProdukTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private VarianProduk $varian;
    private BahanBaku $bahanBaku;

    protected function setUp(): void
    {
        parent::setUp();

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        Role::create(['name' => 'admin', 'guard_name' => 'web']);

        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');

        $kategori     = Kategori::create(['nama' => 'Minuman', 'urutan' => 1]);
        $produk       = Produk::create(['kategori_id' => $kategori->id, 'nama' => 'Susu Kedelai']);
        $this->varian = VarianProduk::create([
            'produk_id'   => $produk->id,
            'nama_varian' => '250ml',
            'harga'       => 5000,
        ]);
        $this->bahanBaku = BahanBaku::create([
            'nama'        => 'Kacang Kedelai',
            'satuan'      => 'kg',
            'kategori_bb' => 'utama',
        ]);
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get(route('app.konversi-produk.index'))
            ->assertRedirect(route('login'));
    }

    public function test_admin_can_view_index(): void
    {
        $this->actingAs($this->admin)
            ->get(route('app.konversi-produk.index'))
            ->assertOk();
    }

    public function test_admin_can_view_create_form(): void
    {
        $this->actingAs($this->admin)
            ->get(route('app.konversi-produk.create'))
            ->assertOk();
    }

    public function test_admin_can_store_konversi_produk(): void
    {
        $this->actingAs($this->admin)
            ->post(route('app.konversi-produk.store'), [
                'varian_produk_id'  => $this->varian->id,
                'bahan_baku_id'     => $this->bahanBaku->id,
                'jumlah_per_satuan' => 0.1,
            ])
            ->assertRedirect(route('app.konversi-produk.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('konversi_produk', [
            'varian_produk_id' => $this->varian->id,
            'bahan_baku_id'    => $this->bahanBaku->id,
        ]);
    }

    public function test_store_rejects_duplicate_pair(): void
    {
        KonversiProduk::create([
            'varian_produk_id'  => $this->varian->id,
            'bahan_baku_id'     => $this->bahanBaku->id,
            'jumlah_per_satuan' => 0.1,
        ]);

        $this->actingAs($this->admin)
            ->post(route('app.konversi-produk.store'), [
                'varian_produk_id'  => $this->varian->id,
                'bahan_baku_id'     => $this->bahanBaku->id,
                'jumlah_per_satuan' => 0.2,
            ])
            ->assertSessionHasErrors('bahan_baku_id');
    }

    public function test_store_rejects_zero_jumlah(): void
    {
        $this->actingAs($this->admin)
            ->post(route('app.konversi-produk.store'), [
                'varian_produk_id'  => $this->varian->id,
                'bahan_baku_id'     => $this->bahanBaku->id,
                'jumlah_per_satuan' => 0,
            ])
            ->assertSessionHasErrors('jumlah_per_satuan');
    }

    public function test_admin_can_view_edit_form(): void
    {
        $konversi = KonversiProduk::create([
            'varian_produk_id'  => $this->varian->id,
            'bahan_baku_id'     => $this->bahanBaku->id,
            'jumlah_per_satuan' => 0.1,
        ]);

        $this->actingAs($this->admin)
            ->get(route('app.konversi-produk.edit', $konversi))
            ->assertOk();
    }

    public function test_admin_can_update_konversi_produk(): void
    {
        $konversi = KonversiProduk::create([
            'varian_produk_id'  => $this->varian->id,
            'bahan_baku_id'     => $this->bahanBaku->id,
            'jumlah_per_satuan' => 0.1,
        ]);

        $this->actingAs($this->admin)
            ->put(route('app.konversi-produk.update', $konversi), [
                'varian_produk_id'  => $this->varian->id,
                'bahan_baku_id'     => $this->bahanBaku->id,
                'jumlah_per_satuan' => 0.25,
            ])
            ->assertRedirect(route('app.konversi-produk.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('konversi_produk', [
            'id'                => $konversi->id,
            'jumlah_per_satuan' => 0.25,
        ]);
    }

    public function test_update_allows_same_pair_on_self(): void
    {
        $konversi = KonversiProduk::create([
            'varian_produk_id'  => $this->varian->id,
            'bahan_baku_id'     => $this->bahanBaku->id,
            'jumlah_per_satuan' => 0.1,
        ]);

        $this->actingAs($this->admin)
            ->put(route('app.konversi-produk.update', $konversi), [
                'varian_produk_id'  => $this->varian->id,
                'bahan_baku_id'     => $this->bahanBaku->id,
                'jumlah_per_satuan' => 0.15,
            ])
            ->assertRedirect(route('app.konversi-produk.index'));
    }

    public function test_admin_can_delete_konversi_produk(): void
    {
        $konversi = KonversiProduk::create([
            'varian_produk_id'  => $this->varian->id,
            'bahan_baku_id'     => $this->bahanBaku->id,
            'jumlah_per_satuan' => 0.1,
        ]);

        $this->actingAs($this->admin)
            ->delete(route('app.konversi-produk.destroy', $konversi))
            ->assertRedirect(route('app.konversi-produk.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('konversi_produk', ['id' => $konversi->id]);
    }
}
```

- [ ] **Step 4.2: Jalankan test — verifikasi gagal (route belum ada)**

```bash
php artisan test --filter=KonversiProdukTest
```

Expected: semua test FAIL dengan `Route [app.konversi-produk.index] not defined` atau HTTP 404.

- [ ] **Step 4.3: Commit**

```bash
git add tests/Feature/Admin/KonversiProdukTest.php
git commit -m "test: add feature tests for KonversiProduk CRUD (failing)"
```

---

## Task 5: Controller + Routes

**Files:**
- Create: `app/Http/Controllers/Admin/KonversiProdukController.php`
- Modify: `routes/web.php`

- [ ] **Step 5.1: Buat KonversiProdukController**

```php
<?php
// app/Http/Controllers/Admin/KonversiProdukController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreKonversiProdukRequest;
use App\Http\Requests\Admin\UpdateKonversiProdukRequest;
use App\Models\BahanBaku;
use App\Models\KonversiProduk;
use App\Models\VarianProduk;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class KonversiProdukController extends Controller
{
    public function index(Request $request): View
    {
        $query = KonversiProduk::with(['varianProduk.produk', 'bahanBaku'])->latest('id');

        if ($varianId = $request->input('varian_produk_id')) {
            $query->where('varian_produk_id', $varianId);
        }

        $konversiProduks = $query->paginate(15)->withQueryString();
        $varianProduks   = VarianProduk::with('produk')->orderBy('produk_id')->get();

        return view('admin.konversi-produk.index', compact('konversiProduks', 'varianProduks'));
    }

    public function create(): View
    {
        $konversiProduk = new KonversiProduk();
        $varianProduks  = VarianProduk::with('produk')->orderBy('produk_id')->get();
        $bahanBakus     = BahanBaku::where('is_active', true)->orderBy('nama')->get();

        return view('admin.konversi-produk.create', compact('konversiProduk', 'varianProduks', 'bahanBakus'));
    }

    public function store(StoreKonversiProdukRequest $request): RedirectResponse
    {
        KonversiProduk::create($request->validated());

        return redirect()->route('app.konversi-produk.index')
            ->with('success', 'Konversi produk berhasil ditambahkan.');
    }

    public function edit(KonversiProduk $konversiProduk): View
    {
        $varianProduks = VarianProduk::with('produk')->orderBy('produk_id')->get();
        $bahanBakus    = BahanBaku::where('is_active', true)->orderBy('nama')->get();

        return view('admin.konversi-produk.edit', compact('konversiProduk', 'varianProduks', 'bahanBakus'));
    }

    public function update(UpdateKonversiProdukRequest $request, KonversiProduk $konversiProduk): RedirectResponse
    {
        $konversiProduk->update($request->validated());

        return redirect()->route('app.konversi-produk.index')
            ->with('success', 'Konversi produk berhasil diperbarui.');
    }

    public function destroy(KonversiProduk $konversiProduk): RedirectResponse
    {
        $konversiProduk->delete();

        return redirect()->route('app.konversi-produk.index')
            ->with('success', 'Konversi produk berhasil dihapus.');
    }
}
```

- [ ] **Step 5.2: Tambahkan import dan route di `routes/web.php`**

Tambahkan import di bagian atas file (bersama import Admin lainnya):
```php
use App\Http\Controllers\Admin\KonversiProdukController;
```

Tambahkan baris berikut di dalam grup `role:admin`, setelah route `bahan-keluar`:
```php
Route::resource('konversi-produk', KonversiProdukController::class)
    ->parameters(['konversi-produk' => 'konversiProduk'])
    ->except(['show']);
```

- [ ] **Step 5.3: Jalankan test — verifikasi pass**

```bash
php artisan test --filter=KonversiProdukTest
```

Expected: semua test PASS (views belum ada tapi Laravel akan melempar ViewException, bukan 404 — test masih fail karena view missing). Lanjut ke Task 6 untuk buat views.

> **Catatan:** Test yang menggunakan `assertOk()` akan fail sampai views dibuat di Task 6–7. Test `assertRedirect` dan `assertDatabaseHas` sudah bisa pass sekarang.

- [ ] **Step 5.4: Commit**

```bash
git add app/Http/Controllers/Admin/KonversiProdukController.php routes/web.php
git commit -m "feat: add KonversiProdukController and resource routes"
```

---

## Task 6: Form Views (_form, create, edit)

**Files:**
- Create: `resources/views/admin/konversi-produk/_form.blade.php`
- Create: `resources/views/admin/konversi-produk/create.blade.php`
- Create: `resources/views/admin/konversi-produk/edit.blade.php`

- [ ] **Step 6.1: Buat `_form.blade.php`**

```blade
{{-- resources/views/admin/konversi-produk/_form.blade.php --}}
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

<div class="space-y-5"
     x-data="{
         selectedBB: '{{ old('bahan_baku_id', $konversiProduk->bahan_baku_id ?? '') }}',
         satuanMap: {{ Js::from($bahanBakus->pluck('satuan', 'id')) }},
         get satuan() { return this.satuanMap[this.selectedBB] ?? '—' }
     }">

    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1.5">
            Varian Produk <span class="text-red-500">*</span>
        </label>
        <select name="varian_produk_id"
                class="w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm bg-white
                       focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 focus:outline-none
                       @error('varian_produk_id') border-red-400 @enderror">
            <option value="">— Pilih varian produk —</option>
            @foreach ($varianProduks as $varian)
                <option value="{{ $varian->id }}"
                        @selected(old('varian_produk_id', $konversiProduk->varian_produk_id) == $varian->id)>
                    {{ $varian->produk->nama }} — {{ $varian->nama_varian }}{{ $varian->ukuran ? ' (' . $varian->ukuran . ')' : '' }}
                </option>
            @endforeach
        </select>
        @error('varian_produk_id')
            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1.5">
            Bahan Baku <span class="text-red-500">*</span>
        </label>
        <select name="bahan_baku_id" x-model="selectedBB"
                class="w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm bg-white
                       focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 focus:outline-none
                       @error('bahan_baku_id') border-red-400 @enderror">
            <option value="">— Pilih bahan baku —</option>
            @foreach ($bahanBakus as $bb)
                <option value="{{ $bb->id }}"
                        @selected(old('bahan_baku_id', $konversiProduk->bahan_baku_id) == $bb->id)>
                    {{ $bb->nama }} ({{ $bb->satuan }})
                </option>
            @endforeach
        </select>
        @error('bahan_baku_id')
            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1.5">
            Jumlah per Satuan <span class="text-red-500">*</span>
        </label>
        <div class="relative">
            <input type="number" name="jumlah_per_satuan" step="0.0001" min="0.0001"
                   value="{{ old('jumlah_per_satuan', $konversiProduk->jumlah_per_satuan ?? '') }}"
                   placeholder="Contoh: 0.1"
                   class="w-full rounded-lg border border-slate-200 px-3 py-2.5 pr-20 text-sm
                          focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 focus:outline-none
                          @error('jumlah_per_satuan') border-red-400 @enderror">
            <span class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-xs text-slate-400"
                  x-text="satuan">—</span>
        </div>
        <p class="mt-1 text-xs text-slate-400">
            Jumlah bahan baku yang dibutuhkan per 1 unit varian produk terjual
        </p>
        @error('jumlah_per_satuan')
            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
        @enderror
    </div>

</div>

<div class="mt-8 flex items-center justify-end gap-3 border-t border-slate-100 pt-6">
    <a href="{{ route('app.konversi-produk.index') }}"
       class="px-4 py-2 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-100 transition">
        Batal
    </a>
    <button type="submit"
            class="px-5 py-2 rounded-lg bg-emerald-500 hover:bg-emerald-600 text-white text-sm font-medium transition">
        {{ $submitLabel ?? 'Simpan' }}
    </button>
</div>
```

- [ ] **Step 6.2: Buat `create.blade.php`**

```blade
{{-- resources/views/admin/konversi-produk/create.blade.php --}}
<x-layouts.admin title="Tambah Konversi Produk" subtitle="Atur rasio kebutuhan bahan baku per satuan varian produk.">
    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm px-8 py-7">
            <form method="POST" action="{{ route('app.konversi-produk.store') }}">
                @include('admin.konversi-produk._form', ['submitLabel' => 'Simpan Konversi'])
            </form>
        </div>
    </div>
</x-layouts.admin>
```

- [ ] **Step 6.3: Buat `edit.blade.php`**

```blade
{{-- resources/views/admin/konversi-produk/edit.blade.php --}}
<x-layouts.admin title="Edit Konversi Produk" subtitle="Ubah rasio kebutuhan bahan baku per satuan varian produk.">
    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm px-8 py-7">
            <form method="POST" action="{{ route('app.konversi-produk.update', $konversiProduk) }}">
                @method('PUT')
                @include('admin.konversi-produk._form', ['submitLabel' => 'Perbarui Konversi'])
            </form>
        </div>
    </div>
</x-layouts.admin>
```

- [ ] **Step 6.4: Commit**

```bash
git add resources/views/admin/konversi-produk/
git commit -m "feat: add KonversiProduk form views (create, edit, _form)"
```

---

## Task 7: Index View

**Files:**
- Create: `resources/views/admin/konversi-produk/index.blade.php`

- [ ] **Step 7.1: Buat `index.blade.php`**

```blade
{{-- resources/views/admin/konversi-produk/index.blade.php --}}
<x-layouts.admin title="Konversi Produk" subtitle="Rasio kebutuhan bahan baku per satuan varian produk terjual.">

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <h3 class="text-sm font-bold text-slate-900">Daftar Konversi Produk</h3>
            <a href="{{ route('app.konversi-produk.create') }}"
               class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg bg-emerald-500 hover:bg-emerald-600 text-white text-sm font-medium transition">
                + Tambah Konversi
            </a>
        </div>

        <div class="px-6 py-3 border-b border-slate-100">
            <form method="GET" action="{{ route('app.konversi-produk.index') }}" class="flex gap-3">
                <select name="varian_produk_id" onchange="this.form.submit()"
                        class="min-w-[240px] px-3 py-2 rounded-lg border border-slate-200 text-sm bg-white focus:border-emerald-500 focus:outline-none">
                    <option value="">Semua Varian Produk</option>
                    @foreach ($varianProduks as $varian)
                        <option value="{{ $varian->id }}"
                                @selected(request('varian_produk_id') == $varian->id)>
                            {{ $varian->produk->nama }} — {{ $varian->nama_varian }}
                        </option>
                    @endforeach
                </select>
                @if (request()->filled('varian_produk_id'))
                    <a href="{{ route('app.konversi-produk.index') }}"
                       class="px-4 py-2 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-100 transition">
                        Reset
                    </a>
                @endif
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50">
                    <tr class="text-left text-xs uppercase tracking-wide text-slate-500">
                        <th class="px-6 py-3 font-medium">Varian Produk</th>
                        <th class="px-6 py-3 font-medium">Bahan Baku</th>
                        <th class="px-6 py-3 font-medium text-right">Jumlah per Satuan</th>
                        <th class="px-6 py-3 font-medium text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($konversiProduks as $item)
                        <tr class="hover:bg-slate-50/50">
                            <td class="px-6 py-4">
                                <p class="text-sm font-semibold text-slate-900">{{ $item->varianProduk->nama_varian }}</p>
                                <p class="text-xs text-slate-400">{{ $item->varianProduk->produk->nama }}</p>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-sm font-semibold text-slate-900">{{ $item->bahanBaku->nama }}</p>
                                <p class="text-xs text-slate-400">{{ $item->bahanBaku->satuan }}</p>
                            </td>
                            <td class="px-6 py-4 text-right tabular-nums">
                                <span class="text-sm font-semibold text-slate-900">
                                    {{ $item->jumlah_per_satuan + 0 }}
                                </span>
                                <span class="text-xs text-slate-400 ml-1">{{ $item->bahanBaku->satuan }}</span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="inline-flex items-center gap-3">
                                    <a href="{{ route('app.konversi-produk.edit', $item) }}"
                                       class="text-sm font-medium text-emerald-600 hover:text-emerald-700">Edit</a>
                                    <form method="POST"
                                          action="{{ route('app.konversi-produk.destroy', $item) }}"
                                          class="inline"
                                          onsubmit="return confirm('Hapus konversi ini?')">
                                        @csrf @method('DELETE')
                                        <button type="submit"
                                                class="text-sm font-medium text-red-500 hover:text-red-600">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-16 text-center text-sm text-slate-400">
                                Belum ada data konversi.
                                <a href="{{ route('app.konversi-produk.create') }}"
                                   class="text-emerald-600 hover:underline">Tambahkan sekarang.</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($konversiProduks->hasPages())
            <div class="px-6 py-4 border-t border-slate-100 flex items-center justify-between">
                <p class="text-xs text-slate-400">
                    Menampilkan {{ $konversiProduks->firstItem() }}–{{ $konversiProduks->lastItem() }}
                    dari {{ $konversiProduks->total() }} data
                </p>
                {{ $konversiProduks->links() }}
            </div>
        @endif
    </div>

</x-layouts.admin>
```

- [ ] **Step 7.2: Jalankan test — verifikasi semua pass**

```bash
php artisan test --filter=KonversiProdukTest
```

Expected: semua 9 test PASS.

- [ ] **Step 7.3: Commit**

```bash
git add resources/views/admin/konversi-produk/index.blade.php
git commit -m "feat: add KonversiProduk index view"
```

---

## Task 8: Icon + Sidebar Menu Item

**Files:**
- Modify: `resources/views/components/admin/icon.blade.php`
- Modify: `resources/views/components/admin/sidebar.blade.php`

- [ ] **Step 8.1: Tambahkan icon `arrows-right-left` di `icon.blade.php`**

Tambahkan case berikut sebelum `@case('logout')` (agar terurut secara logis):

```blade
@case('arrows-right-left')
    <svg {{ $attributes }} fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round"
            d="M7.5 21L3 16.5m0 0L7.5 12M3 16.5h13.5m0-13.5L21 7.5m0 0L16.5 3M21 7.5H7.5" />
    </svg>
@break
```

- [ ] **Step 8.2: Tambahkan menu item di sidebar admin**

Di `resources/views/components/admin/sidebar.blade.php`, dalam array `$groups` untuk role admin, cari grup `'label' => 'Katalog'`. Tambahkan baris berikut setelah entry `varian-produk`:

```php
['route' => 'app.konversi-produk.index', 'label' => 'Konversi Produk', 'icon' => 'arrows-right-left'],
```

Sehingga grup Katalog menjadi:
```php
[
    'label' => 'Katalog',
    'items' => [
        ['route' => 'app.kategori.index',         'label' => 'Kategori',        'icon' => 'tag'],
        ['route' => 'app.produk.index',           'label' => 'Produk',          'icon' => 'bag'],
        ['route' => 'app.varian-produk.index',    'label' => 'Varian Produk',   'icon' => 'layers'],
        ['route' => 'app.konversi-produk.index',  'label' => 'Konversi Produk', 'icon' => 'arrows-right-left'],
    ],
],
```

- [ ] **Step 8.3: Jalankan full test suite**

```bash
php artisan test
```

Expected: semua test PASS (termasuk test yang sudah ada sebelumnya).

- [ ] **Step 8.4: Commit**

```bash
git add resources/views/components/admin/icon.blade.php resources/views/components/admin/sidebar.blade.php
git commit -m "feat: add arrows-right-left icon and Konversi Produk sidebar menu item"
```

---

## Checklist Verifikasi Akhir

- [ ] `php artisan test` — semua pass
- [ ] Login sebagai admin, kunjungi `/app/konversi-produk` — halaman tampil
- [ ] Menu "Konversi Produk" muncul di sidebar grup Katalog
- [ ] Buat konversi baru → redirect ke index dengan flash success
- [ ] Coba buat konversi duplikat (varian + bahan baku sama) → validation error
- [ ] Coba input jumlah = 0 → validation error
- [ ] Edit konversi dengan pair yang sama → sukses (no false duplicate error)
- [ ] Hapus konversi → redirect ke index dengan flash success
- [ ] Filter by varian produk di index bekerja
