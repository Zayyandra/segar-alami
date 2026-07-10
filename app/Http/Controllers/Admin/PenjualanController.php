<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Penjualan;
use App\Models\VarianProduk;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
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

        // Validasi stok tersedia SEBELUM masuk transaksi DB, sambil digabung
        // per varian (kalau varian yang sama muncul >1 kali dalam 1 transaksi).
        $totalPerVarian = [];
        foreach ($request->items as $item) {
            $id = $item['varian_produk_id'];
            $totalPerVarian[$id] = ($totalPerVarian[$id] ?? 0) + $item['jumlah'];
        }

        $variansTerpakai = VarianProduk::with('produk:id,nama')
            ->whereIn('id', array_keys($totalPerVarian))
            ->get()
            ->keyBy('id');

        foreach ($totalPerVarian as $varianId => $jumlahDiminta) {
            $varian = $variansTerpakai->get($varianId);
            if ($varian && $varian->stok < $jumlahDiminta) {
                throw ValidationException::withMessages([
                    'items' => "Stok {$varian->produk->nama} - {$varian->nama_varian} tidak cukup. "
                        . "Stok tersedia: {$varian->stok}, diminta: {$jumlahDiminta}.",
                ]);
            }
        }

        DB::transaction(function () use ($request, $totalPerVarian) {
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

            // Kurangi stok varian produk sesuai jumlah yang terjual
            foreach ($totalPerVarian as $varianId => $jumlahTerjual) {
                VarianProduk::where('id', $varianId)->decrement('stok', $jumlahTerjual);
            }
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

        // Kembalikan dulu stok dari item LAMA (sebelum diedit), supaya
        // perhitungan stok tersedia untuk validasi baru itu adil/akurat.
        $penjualan->load('details');
        $totalLamaPerVarian = [];
        foreach ($penjualan->details as $detail) {
            $id = $detail->varian_produk_id;
            $totalLamaPerVarian[$id] = ($totalLamaPerVarian[$id] ?? 0) + $detail->jumlah;
        }

        $totalBaruPerVarian = [];
        foreach ($request->items as $item) {
            $id = $item['varian_produk_id'];
            $totalBaruPerVarian[$id] = ($totalBaruPerVarian[$id] ?? 0) + $item['jumlah'];
        }

        $semuaVarianId = array_unique(array_merge(
            array_keys($totalLamaPerVarian),
            array_keys($totalBaruPerVarian)
        ));

        $variansTerpakai = VarianProduk::with('produk:id,nama')
            ->whereIn('id', $semuaVarianId)
            ->get()
            ->keyBy('id');

        // Validasi: stok_efektif = stok_sekarang + jumlah_lama (dikembalikan) - jumlah_baru (diminta)
        foreach ($totalBaruPerVarian as $varianId => $jumlahBaru) {
            $varian = $variansTerpakai->get($varianId);
            if (!$varian) {
                continue;
            }
            $jumlahLama   = $totalLamaPerVarian[$varianId] ?? 0;
            $stokEfektif  = $varian->stok + $jumlahLama;

            if ($stokEfektif < $jumlahBaru) {
                throw ValidationException::withMessages([
                    'items' => "Stok {$varian->produk->nama} - {$varian->nama_varian} tidak cukup. "
                        . "Stok tersedia: {$stokEfektif}, diminta: {$jumlahBaru}.",
                ]);
            }
        }

        DB::transaction(function () use ($request, $penjualan, $totalLamaPerVarian, $totalBaruPerVarian) {
            // Kembalikan stok lama
            foreach ($totalLamaPerVarian as $varianId => $jumlahLama) {
                VarianProduk::where('id', $varianId)->increment('stok', $jumlahLama);
            }

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

            // Kurangi stok sesuai item baru
            foreach ($totalBaruPerVarian as $varianId => $jumlahBaru) {
                VarianProduk::where('id', $varianId)->decrement('stok', $jumlahBaru);
            }
        });

        $this->clearDashboardCache();

        return redirect()
            ->route('app.penjualan.show', $penjualan)
            ->with('success', 'Transaksi berhasil diperbarui.');
    }

    // destroy dihapus — penjualan tidak bisa dihapus sesuai arahan
}
