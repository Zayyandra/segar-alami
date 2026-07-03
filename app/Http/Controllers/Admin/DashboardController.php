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
use Illuminate\Support\Facades\Cache;
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
        $bulanKey     = $now->format('Y-m');

        $totalBulanIni = Cache::remember("dashboard.owner.total_bulan_ini.{$bulanKey}", 120, fn() =>
            Penjualan::whereBetween('tanggal', [$bulanIni, $now])->sum('total')
        );

        $totalBulanLalu = Cache::remember("dashboard.owner.total_bulan_lalu.{$bulanKey}", 3600, fn() =>
            Penjualan::whereBetween('tanggal', [$bulanLalu, $endBulanLalu])->sum('total')
        );

        $persenChange = $totalBulanLalu > 0
            ? (($totalBulanIni - $totalBulanLalu) / $totalBulanLalu) * 100
            : 0;

        $jumlahTerjual = Cache::remember("dashboard.owner.jumlah_terjual.{$bulanKey}", 120, fn() =>
            DetailPenjualan::whereHas('penjualan', fn($q) => $q->whereBetween('tanggal', [$bulanIni, $now]))->sum('jumlah')
        );

        $totalBahanBaku = Cache::remember('dashboard.total_bahan_baku', 300, fn() =>
            BahanBaku::where('is_active', true)->count()
        );

        $totalKategori = Cache::remember('dashboard.total_kategori', 300, fn() =>
            Kategori::where('is_active', true)->count()
        );

        // Pakai DB::table() — Collection of stdClass aman di-cache SQLite

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

        // Tren penjualan harian bulan berjalan (untuk line chart)
        $trenHarian = DB::table('penjualan')
            ->whereBetween('tanggal', [$bulanIni, $now])
            ->groupBy('tanggal')
            ->orderBy('tanggal')
            ->selectRaw('tanggal, SUM(total) as total')
            ->get();

        $trenLabels = $trenHarian->map(fn($t) => Carbon::parse($t->tanggal)->translatedFormat('d M'))->values();
        $trenValues = $trenHarian->pluck('total')->map(fn($v) => (float) $v)->values();

        return view('owner.dashboard', compact(
            'totalBulanIni', 'persenChange', 'jumlahTerjual',
            'totalBahanBaku', 'totalKategori', 'volumePerKategori', 'distribusi',
            'trenLabels', 'trenValues'
        ));
    }

    private function adminDashboard(): View
    {
        $now      = Carbon::now();
        $bulanIni = $now->copy()->startOfMonth();
        $todayKey = today()->format('Y-m-d');

        $totalProdukAktif = Cache::remember('dashboard.total_produk_aktif', 300, fn() =>
            Produk::where('is_active', true)->count()
        );

        $totalVarianAktif = Cache::remember('dashboard.total_varian_aktif', 300, fn() =>
            VarianProduk::where('is_active', true)->count()
        );

        $totalBahanBaku = Cache::remember('dashboard.total_bahan_baku', 300, fn() =>
            BahanBaku::where('is_active', true)->count()
        );

        $transaksiHariIni = Cache::remember("dashboard.transaksi_hari_ini.{$todayKey}", 60, fn() =>
            Penjualan::whereDate('tanggal', today())->count()
        );

        $pendapatanBulanIni = Cache::remember("dashboard.pendapatan_bulan_ini.{$todayKey}", 60, fn() =>
            Penjualan::whereBetween('tanggal', [$bulanIni, $now])->sum('total')
        );

        // Produk terjual bulan ini (dalam unit/item)
        $produkTerjualBulanIni = Cache::remember("dashboard.produk_terjual_bulan_ini.{$todayKey}", 60, fn() =>
            DetailPenjualan::whereHas('penjualan', fn($q) =>
                $q->whereBetween('tanggal', [$bulanIni, $now])
            )->sum('jumlah')
        );

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
            'transaksiHariIni', 'pendapatanBulanIni', 'produkTerjualBulanIni',
            'transaksiTerbaru', 'expiredAlert'
        ));
    }
}
