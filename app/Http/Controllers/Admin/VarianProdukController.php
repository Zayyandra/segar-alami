<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreVarianProdukRequest;
use App\Http\Requests\Admin\UpdateVarianProdukRequest;
use App\Models\Produk;
use App\Models\VarianProduk;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VarianProdukController extends Controller
{
    public function index(Request $request): View
    {
        $query = VarianProduk::with('produk:id,nama')->latest('id');

        if ($search = trim((string) $request->input('q'))) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_varian', 'like', "%{$search}%")
                  ->orWhere('ukuran', 'like', "%{$search}%")
                  ->orWhereHas('produk', fn ($p) => $p->where('nama', 'like', "%{$search}%"));
            });
        }

        if ($produkId = $request->integer('produk_id')) {
            $query->where('produk_id', $produkId);
        }

        $variants = $query->paginate(15)->withQueryString();
        $produks  = Produk::orderBy('nama')->get(['id', 'nama']);

        return view('admin.varian-produk.index', compact('variants', 'produks'));
    }

    public function create(): View
    {
        $varian  = new VarianProduk(['is_active' => true]);
        $produks = Produk::where('is_active', true)->orderBy('nama')->get(['id', 'nama']);

        return view('admin.varian-produk.create', compact('varian', 'produks'));
    }

    public function store(StoreVarianProdukRequest $request): RedirectResponse
    {
        VarianProduk::create($request->validated());

        return redirect()
            ->route('app.varian-produk.index')
            ->with('success', 'Varian produk berhasil ditambahkan.');
    }

    public function edit(VarianProduk $varianProduk): View
    {
        $varian  = $varianProduk;
        $produks = Produk::orderBy('nama')->get(['id', 'nama']);

        return view('admin.varian-produk.edit', compact('varian', 'produks'));
    }

    public function update(UpdateVarianProdukRequest $request, VarianProduk $varianProduk): RedirectResponse
    {
        $varianProduk->update($request->validated());

        return redirect()
            ->route('app.varian-produk.index')
            ->with('success', 'Varian produk berhasil diperbarui.');
    }

    public function destroy(VarianProduk $varianProduk): RedirectResponse
    {
        $varianProduk->delete();

        return redirect()
            ->route('app.varian-produk.index')
            ->with('success', 'Varian produk berhasil dihapus.');
    }
}
