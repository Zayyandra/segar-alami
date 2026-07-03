<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Penjualan;
use App\Models\VarianProduk;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PenjualanController extends Controller
{
    private function clearDashboardCache(): void
    {
        $todayKey = today()->format('Y-m-d');
        $monthKey = now()->format('Y-m');

        Cache::forget("dashboard.transaksi_hari_ini.{$todayKey}");
        Cache::forget("dashboard.pendapatan_hari_ini.{$todayKey}");
        Cache::forget("dashboard.pendapatan_bulan_ini.{$todayKey}");
        Cache::forget("dashboard.produk_terjual_bulan_ini.{$todayKey}");
        Cache::forget("dashboard.owner.total_bulan_ini.{$monthKey}");
        Cache::forget("dashboard.owner.jumlah_terjual.{$monthKey}");
        Cache::forget("dashboard.owner.volume_kategori.{$monthKey}");
    }

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

        return view('admin.penjualan.index', compact(
            'penjualans', 'totalOmzetHariIni', 'transaksiHariIni', 'totalProdukTerjual'
        ));
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
            'tanggal'                  => ['required', 'date', 'before_or_equal:today'],
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
            'tanggal.before_or_equal'           => 'Tanggal tidak boleh melebihi hari ini.',
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

        $this->clearDashboardCache();

        return redirect()
            ->route('app.penjualan.index')
            ->with('success', 'Transaksi penjualan berhasil dicatat.');
    }

    public function show(Penjualan $penjualan): View
    {
        $penjualan->load('details.varianProduk.produk', 'user');

        $varianIds = $penjualan->details->pluck('varianProduk.id')->unique();

        $semuaKonversi = \App\Models\KonversiProduk::with('bahanBaku')
            ->whereIn('varian_produk_id', $varianIds)
            ->get()
            ->groupBy('varian_produk_id');

        $estimasi = [];

        foreach ($penjualan->details as $detail) {
            $varian    = $detail->varianProduk;
            $konversis = $semuaKonversi->get($varian->id, collect());

            if ($konversis->isEmpty()) {
                $wakilIds = \App\Models\VarianProduk::where('produk_id', $varian->produk_id)
                    ->where('ukuran', $varian->ukuran)
                    ->pluck('id');

                foreach ($wakilIds as $wakilId) {
                    if ($semuaKonversi->has($wakilId)) {
                        $konversis = $semuaKonversi->get($wakilId);
                        break;
                    }
                }
            }

            foreach ($konversis as $konversi) {
                $id    = $konversi->bahan_baku_id;
                $total = $detail->jumlah * $konversi->jumlah_per_satuan;

                if (isset($estimasi[$id])) {
                    $estimasi[$id]['total'] += $total;
                } else {
                    $estimasi[$id] = [
                        'nama'   => $konversi->bahanBaku->nama,
                        'satuan' => $konversi->bahanBaku->satuan,
                        'total'  => $total,
                    ];
                }
            }
        }

        return view('admin.penjualan.show', compact('penjualan', 'estimasi'));
    }

    public function edit(Penjualan $penjualan): View
    {
        $penjualan->load('details.varianProduk');

        $variants = VarianProduk::with('produk:id,nama')
            ->where('is_active', true)
            ->orderBy('produk_id')
            ->get();

        return view('admin.penjualan.edit', compact('penjualan', 'variants'));
    }

    public function update(Request $request, Penjualan $penjualan): RedirectResponse
    {
        $request->validate([
            'tanggal'                  => ['required', 'date', 'before_or_equal:today'],
            'keterangan'               => ['nullable', 'string', 'max:500'],
            'items'                    => ['required', 'array', 'min:1'],
            'items.*.varian_produk_id' => ['required', 'exists:varian_produk,id'],
            'items.*.jumlah'           => ['required', 'integer', 'min:1'],
            'items.*.harga_satuan'     => ['required', 'numeric', 'min:0'],
        ], [
            'items.required'                    => 'Minimal satu item harus diisi.',
            'items.*.varian_produk_id.required' => 'Pilih produk untuk setiap item.',
            'items.*.jumlah.min'                => 'Jumlah minimal 1.',
            'tanggal.before_or_equal'           => 'Tanggal tidak boleh melebihi hari ini.',
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

        $this->clearDashboardCache();

        return redirect()
            ->route('app.penjualan.show', $penjualan)
            ->with('success', 'Transaksi berhasil diperbarui.');
    }

    // destroy dihapus — penjualan tidak bisa dihapus sesuai arahan
}
