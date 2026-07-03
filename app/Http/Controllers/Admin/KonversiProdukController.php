<?php
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
        $varianProduks = VarianProduk::with('produk')->orderBy('produk_id')->get();
        $bahanBakus    = BahanBaku::where('is_active', true)
            ->where('kategori_bb', 'utama')
            ->orderBy('nama')
            ->get();

        return view('admin.konversi-produk.create', compact('varianProduks', 'bahanBakus'));
    }

    public function store(StoreKonversiProdukRequest $request): RedirectResponse
    {
        $varianId = $request->validated()['varian_produk_id'];

        foreach ($request->validated()['items'] as $item) {
            KonversiProduk::updateOrCreate(
                [
                    'varian_produk_id' => $varianId,
                    'bahan_baku_id'    => $item['bahan_baku_id'],
                ],
                [
                    'jumlah_per_satuan' => $item['jumlah_per_satuan'],
                ]
            );
        }

        return redirect()->route('app.konversi-produk.index')
            ->with('success', 'Konversi produk berhasil ditambahkan.');
    }

    public function edit(KonversiProduk $konversiProduk): View
    {
        $varianProduks = VarianProduk::with('produk')->orderBy('produk_id')->get();
        $bahanBakus    = BahanBaku::where('is_active', true)
            ->where('kategori_bb', 'utama')
            ->orderBy('nama')
            ->get();

        // Load semua konversi untuk varian yang sama
        $existingKonversi = KonversiProduk::where('varian_produk_id', $konversiProduk->varian_produk_id)->get();

        return view('admin.konversi-produk.edit', compact('konversiProduk', 'varianProduks', 'bahanBakus', 'existingKonversi'));
    }

    public function update(UpdateKonversiProdukRequest $request, KonversiProduk $konversiProduk): RedirectResponse
    {
        $varianIdBaru = $request->validated()['varian_produk_id'];
        $varianIdLama = $konversiProduk->varian_produk_id;

        \Illuminate\Support\Facades\DB::transaction(function () use ($request, $varianIdBaru, $varianIdLama) {
            // Hapus konversi varian lama DAN varian tujuan, lalu buat ulang
            KonversiProduk::whereIn('varian_produk_id', array_unique([$varianIdLama, $varianIdBaru]))->delete();

            foreach ($request->validated()['items'] as $item) {
                KonversiProduk::create([
                    'varian_produk_id'  => $varianIdBaru,
                    'bahan_baku_id'     => $item['bahan_baku_id'],
                    'jumlah_per_satuan' => $item['jumlah_per_satuan'],
                ]);
            }
        });

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
