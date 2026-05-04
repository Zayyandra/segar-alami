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
