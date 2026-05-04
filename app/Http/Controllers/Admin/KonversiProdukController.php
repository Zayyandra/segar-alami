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
