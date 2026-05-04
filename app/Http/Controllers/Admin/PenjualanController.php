<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Penjualan;
use App\Models\VarianProduk;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PenjualanController extends Controller
{
    public function index(Request $request): View
    {
        $query = Penjualan::with('user')->latest('tanggal')->latest('id');

        if ($search = trim((string) $request->input('q'))) {
            $query->where('keterangan', 'like', "%{$search}%");
        }

        if ($tanggal = $request->input('tanggal')) {
            $query->whereDate('tanggal', $tanggal);
        }

        $penjualans         = $query->paginate(15)->withQueryString();
        $totalOmzetHariIni  = Penjualan::whereDate('tanggal', today())->sum('total');
        $transaksiHariIni   = Penjualan::whereDate('tanggal', today())->count();
        $totalProdukTerjual = \App\Models\DetailPenjualan::whereHas('penjualan', fn($q) => $q->whereDate('tanggal', today()))->sum('jumlah');

        return view('admin.penjualan.index', compact('penjualans', 'totalOmzetHariIni', 'transaksiHariIni', 'totalProdukTerjual'));
    }

    public function create(): View
    {
        $variants = VarianProduk::with('produk:id,nama')
            ->where('is_active', true)
            ->orderBy('produk_id')
            ->get();

        return view('admin.penjualan.create', compact('variants'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'tanggal'                  => ['required', 'date'],
            'keterangan'               => ['nullable', 'string', 'max:500'],
            'items'                    => ['required', 'array', 'min:1'],
            'items.*.varian_produk_id' => ['required', 'exists:varian_produk,id'],
            'items.*.jumlah'           => ['required', 'integer', 'min:1'],
            'items.*.harga_satuan'     => ['required', 'numeric', 'min:0'],
        ], [
            'items.required'                    => 'Minimal satu item harus diisi.',
            'items.*.varian_produk_id.required' => 'Pilih produk untuk setiap item.',
            'items.*.jumlah.required'           => 'Jumlah wajib diisi.',
            'items.*.jumlah.min'                => 'Jumlah minimal 1.',
        ]);

        DB::transaction(function () use ($request) {
            $total = 0;
            $items = [];

            foreach ($request->items as $item) {
                $subTotal  = $item['jumlah'] * $item['harga_satuan'];
                $total    += $subTotal;
                $items[]   = [
                    'varian_produk_id' => $item['varian_produk_id'],
                    'jumlah'           => $item['jumlah'],
                    'harga_satuan'     => $item['harga_satuan'],
                    'sub_total'        => $subTotal,
                ];
            }

            $penjualan = Penjualan::create([
                'user_id'    => auth()->id(),
                'tanggal'    => $request->tanggal,
                'keterangan' => $request->keterangan,
                'total'      => $total,
            ]);

            $penjualan->details()->createMany($items);
        });

        return redirect()
            ->route('app.penjualan.index')
            ->with('success', 'Transaksi penjualan berhasil dicatat.');
    }

    public function show(Penjualan $penjualan): View
    {
        $penjualan->load('details.varianProduk.produk', 'user');

        return view('admin.penjualan.show', compact('penjualan'));
    }

    public function edit(Penjualan $penjualan): View
    {
        $penjualan->load('details.varianProduk');

        $variants = VarianProduk::with('produk:id,nama')
            ->where('is_active', true)
            ->orderBy('produk_id')
            ->get();

        $totalOmzetHariIni  = Penjualan::whereDate('tanggal', today())->sum('total');
        $totalProdukTerjual = \App\Models\DetailPenjualan::whereHas('penjualan', fn($q) => $q->whereDate('tanggal', today()))->sum('jumlah');
// tambah ke compact
        return view('admin.penjualan.edit', compact('penjualan', 'variants', 'totalOmzetHariIni', 'totalProdukTerjual'));
    }

    public function update(Request $request, Penjualan $penjualan): RedirectResponse
    {
        $request->validate([
            'tanggal'                  => ['required', 'date'],
            'keterangan'               => ['nullable', 'string', 'max:500'],
            'items'                    => ['required', 'array', 'min:1'],
            'items.*.varian_produk_id' => ['required', 'exists:varian_produk,id'],
            'items.*.jumlah'           => ['required', 'integer', 'min:1'],
            'items.*.harga_satuan'     => ['required', 'numeric', 'min:0'],
        ], [
            'items.required'                    => 'Minimal satu item harus diisi.',
            'items.*.varian_produk_id.required' => 'Pilih produk untuk setiap item.',
            'items.*.jumlah.min'                => 'Jumlah minimal 1.',
        ]);

        DB::transaction(function () use ($request, $penjualan) {
            $total = 0;
            $items = [];

            foreach ($request->items as $item) {
                $subTotal  = $item['jumlah'] * $item['harga_satuan'];
                $total    += $subTotal;
                $items[]   = [
                    'varian_produk_id' => $item['varian_produk_id'],
                    'jumlah'           => $item['jumlah'],
                    'harga_satuan'     => $item['harga_satuan'],
                    'sub_total'        => $subTotal,
                ];
            }

            $penjualan->update([
                'tanggal'    => $request->tanggal,
                'keterangan' => $request->keterangan,
                'total'      => $total,
            ]);

            $penjualan->details()->delete();
            $penjualan->details()->createMany($items);
        });

        return redirect()
            ->route('app.penjualan.show', $penjualan)
            ->with('success', 'Transaksi berhasil diperbarui.');
    }

    public function destroy(Penjualan $penjualan): RedirectResponse
    {
        $penjualan->delete();

        return redirect()
            ->route('app.penjualan.index')
            ->with('success', 'Transaksi berhasil dihapus.');
    }
}
