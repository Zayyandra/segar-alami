<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ProdukRequest;
use App\Models\Kategori;
use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProdukController extends Controller
{
    public function index(Request $request)
    {
        $produk = Produk::query()
            ->with('kategori')
            ->when($request->search, fn($q, $s) => $q->where('nama', 'like', "%{$s}%"))
            ->when($request->kategori, fn($q, $k) => $q->where('kategori_id', $k))
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        $kategori    = Kategori::where('is_active', true)->orderBy('urutan')->get();
        $totalVarian = \App\Models\VarianProduk::where('is_active', true)->count();

        return view('admin.produk.index', compact('produk', 'kategori', 'totalVarian'));
    }

    public function create()
    {
        $kategori = Kategori::where('is_active', true)->orderBy('urutan')->get();
        return view('admin.produk.create', compact('kategori'));
    }

    public function store(ProdukRequest $request)
    {
        $data = $request->validated();

        if ($request->hasFile('foto')) {
            $data['foto'] = $this->uploadFoto($request->file('foto'), $data['nama']);
        }

        Produk::create($data);

        return redirect()->route('app.produk.index')
            ->with('success', 'Produk berhasil ditambahkan.');
    }

    public function edit(Produk $produk)
    {
        $kategori = Kategori::where('is_active', true)->orderBy('urutan')->get();
        return view('admin.produk.edit', compact('produk', 'kategori'));
    }

    public function update(ProdukRequest $request, Produk $produk)
    {
        $data = $request->validated();

        if ($request->hasFile('foto')) {
            // Hapus foto lama
            if ($produk->foto) {
                Storage::disk('public')->delete($produk->foto);
            }
            $data['foto'] = $this->uploadFoto($request->file('foto'), $data['nama']);
        }

        $produk->update($data);

        return redirect()->route('app.produk.index')
            ->with('success', 'Produk berhasil diperbarui.');
    }

    public function destroy(Produk $produk)
    {
        if ($produk->varian()->exists()) {
            return back()->with('error', 'Produk tidak bisa dihapus karena masih punya varian.');
        }

        if ($produk->foto) {
            Storage::disk('public')->delete($produk->foto);
        }

        $produk->delete();

        return redirect()->route('app.produk.index')
            ->with('success', 'Produk berhasil dihapus.');
    }

    private function uploadFoto($file, string $nama): string
    {
        $filename = time() . '_' . Str::slug($nama) . '.' . $file->getClientOriginalExtension();
        return $file->storeAs('produk', $filename, 'public');
    }
}
