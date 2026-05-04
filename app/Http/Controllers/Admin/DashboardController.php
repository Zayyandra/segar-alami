<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BahanBaku;
use App\Models\BahanMasuk;
use App\Models\DetailPenjualan;
use App\Models\Kategori;
use App\Models\Penjualan;
use App\Models\Produk;
use App\Models\VarianProduk;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        Carbon::setLocale('id');

        if (auth()->user()->hasRole('owner')) {
            return $this->ownerDashboard();
        }

        return $this->adminDashboard();
    }

    private function ownerDashboard(): View
    {
        $now          = Carbon::now();
        $bulanIni     = $now->copy()->startOfMonth();
        $bulanLalu    = $now->copy()->subMonth()->startOfMonth();
        $endBulanLalu = $now->copy()->subMonth()->endOfMonth();

        $totalBulanIni  = Penjualan::whereBetween('tanggal', [$bulanIni, $now])->sum('total');
        $totalBulanLalu = Penjualan::whereBetween('tanggal', [$bulanLalu, $endBulanLalu])->sum('total');
        $persenChange   = $totalBulanLalu > 0
            ? (($totalBulanIni - $totalBulanLalu) / $totalBulanLalu) * 100
            : 0;

        $jumlahTerjual  = DetailPenjualan::whereHas('penjualan', fn($q) => $q->whereBetween('tanggal', [$bulanIni, $now]))->sum('jumlah');
        $totalBahanBaku = BahanBaku::where('is_active', true)->count();
        $totalKategori  = Kategori::where('is_active', true)->count();

        $volumePerKategori = DB::table('detail_penjualan')
            ->join('penjualan', 'detail_penjualan.penjualan_id', '=', 'penjualan.id')
            ->join('varian_produk', 'detail_penjualan.varian_produk_id', '=', 'varian_produk.id')
            ->join('produk', 'varian_produk.produk_id', '=', 'produk.id')
            ->join('kategori', 'produk.kategori_id', '=', 'kategori.id')
            ->whereBetween('penjualan.tanggal', [$bulanIni, $now])
            ->groupBy('kategori.id', 'kategori.nama')
            ->selectRaw('kategori.nama as kategori, SUM(detail_penjualan.sub_total) as total')
            ->orderByDesc('total')
            ->get();

        $totalAll   = $volumePerKategori->sum('total');
        $distribusi = $volumePerKategori->map(fn($item) => [
            'kategori' => $item->kategori,
            'total'    => $item->total,
            'persen'   => $totalAll > 0 ? round(($item->total / $totalAll) * 100, 1) : 0,
        ]);

        return view('owner.dashboard', compact(
            'totalBulanIni', 'persenChange', 'jumlahTerjual',
            'totalBahanBaku', 'totalKategori', 'volumePerKategori', 'distribusi'
        ));
    }

    private function adminDashboard(): View
    {
        $now      = Carbon::now();
        $bulanIni = $now->copy()->startOfMonth();

        $totalProdukAktif   = Produk::where('is_active', true)->count();
        $totalVarianAktif   = VarianProduk::where('is_active', true)->count();
        $totalBahanBaku     = BahanBaku::where('is_active', true)->count();
        $totalKategori      = Kategori::where('is_active', true)->count();
        $transaksiHariIni   = Penjualan::whereDate('tanggal', today())->count();
        $pendapatanHariIni  = Penjualan::whereDate('tanggal', today())->sum('total');
        $pendapatanBulanIni = Penjualan::whereBetween('tanggal', [$bulanIni, $now])->sum('total');

        $transaksiTerbaru = Penjualan::with('user')
            ->latest('tanggal')->latest('id')
            ->limit(5)
            ->get();

        $expiredAlert = BahanMasuk::with('bahanBaku')
            ->whereNotNull('tanggal_kadaluarsa')
            ->where(function ($q) {
                $q->whereDate('tanggal_kadaluarsa', '<', today())
                  ->orWhere(function ($q2) {
                      $q2->whereDate('tanggal_kadaluarsa', '>=', today())
                         ->whereDate('tanggal_kadaluarsa', '<=', today()->addDays(7));
                  });
            })
            ->orderBy('tanggal_kadaluarsa')
            ->get();

        return view('admin.dashboard', compact(
            'totalProdukAktif', 'totalVarianAktif', 'totalBahanBaku',
            'totalKategori', 'transaksiHariIni', 'pendapatanHariIni',
            'pendapatanBulanIni', 'transaksiTerbaru', 'expiredAlert'
        ));
    }
}
