<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Penjualan;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class LaporanPenjualanController extends Controller
{
    public function index(Request $request): View
    {
        Carbon::setLocale('id');

        $bulan = $request->input('bulan', now()->format('Y-m'));
        [$tahun, $bln] = explode('-', $bulan);

        $start = Carbon::createFromDate($tahun, $bln, 1)->startOfMonth();
        $end   = $start->copy()->endOfMonth();

        $totalPendapatan = Penjualan::whereBetween('tanggal', [$start, $end])->sum('total');
        $totalTransaksi  = Penjualan::whereBetween('tanggal', [$start, $end])->count();
        $rataPerHari     = $totalTransaksi > 0 ? $totalPendapatan / $start->daysInMonth : 0;

        $trenHarian = Penjualan::whereBetween('tanggal', [$start, $end])
            ->selectRaw('DATE(tanggal) as tgl, SUM(total) as total')
            ->groupBy('tgl')->orderBy('tgl')->get()->keyBy('tgl');

        $days = []; $totals = [];
        for ($d = $start->copy(); $d->lte($end); $d->addDay()) {
            $key = $d->format('Y-m-d');
            $days[]   = $d->format('d');
            $totals[] = isset($trenHarian[$key]) ? (float) $trenHarian[$key]->total : 0;
        }

        $produkTerlaris = DB::table('detail_penjualan')
            ->join('penjualan', 'detail_penjualan.penjualan_id', '=', 'penjualan.id')
            ->join('varian_produk', 'detail_penjualan.varian_produk_id', '=', 'varian_produk.id')
            ->join('produk', 'varian_produk.produk_id', '=', 'produk.id')
            ->join('kategori', 'produk.kategori_id', '=', 'kategori.id')
            ->whereBetween('penjualan.tanggal', [$start, $end])
            ->selectRaw('CONCAT(produk.nama, " - ", varian_produk.nama_varian) as nama, kategori.nama as kategori, SUM(detail_penjualan.jumlah) as total_unit, SUM(detail_penjualan.sub_total) as total_pendapatan')
            ->groupBy('produk.id', 'varian_produk.id', 'produk.nama', 'varian_produk.nama_varian', 'kategori.nama')
            ->orderByDesc('total_unit')->limit(10)->get();

        $totalUnit  = $produkTerlaris->sum('total_unit');
        $transaksis = Penjualan::with('user')
            ->whereBetween('tanggal', [$start, $end])
            ->latest('tanggal')->paginate(10)->withQueryString();

        return view('owner.laporan-penjualan', compact(
            'bulan', 'start', 'totalPendapatan', 'totalTransaksi',
            'rataPerHari', 'days', 'totals', 'produkTerlaris', 'totalUnit', 'transaksis'
        ));
    }

    public function exportPdf(Request $request)
    {
        Carbon::setLocale('id');

        $bulan = $request->input('bulan', now()->format('Y-m'));
        [$tahun, $bln] = explode('-', $bulan);

        $start = Carbon::createFromDate($tahun, $bln, 1)->startOfMonth();
        $end   = $start->copy()->endOfMonth();

        $totalPendapatan = Penjualan::whereBetween('tanggal', [$start, $end])->sum('total');
        $totalTransaksi  = Penjualan::whereBetween('tanggal', [$start, $end])->count();
        $rataPerHari     = $totalTransaksi > 0 ? $totalPendapatan / $start->daysInMonth : 0;

        $produkTerlaris = DB::table('detail_penjualan')
            ->join('penjualan', 'detail_penjualan.penjualan_id', '=', 'penjualan.id')
            ->join('varian_produk', 'detail_penjualan.varian_produk_id', '=', 'varian_produk.id')
            ->join('produk', 'varian_produk.produk_id', '=', 'produk.id')
            ->whereBetween('penjualan.tanggal', [$start, $end])
            ->selectRaw('CONCAT(produk.nama, " - ", varian_produk.nama_varian) as nama, SUM(detail_penjualan.jumlah) as total_unit, SUM(detail_penjualan.sub_total) as total_pendapatan')
            ->groupBy('produk.id', 'varian_produk.id', 'produk.nama', 'varian_produk.nama_varian')
            ->orderByDesc('total_unit')->limit(10)->get();

        $transaksis = Penjualan::with('user')
            ->whereBetween('tanggal', [$start, $end])
            ->latest('tanggal')->get();

        $pdf = Pdf::loadView('owner.pdf.laporan-penjualan', compact(
            'start', 'end', 'totalPendapatan', 'totalTransaksi',
            'rataPerHari', 'transaksis', 'produkTerlaris'
        ))->setPaper('a4', 'portrait');

        return $pdf->download('laporan-penjualan-' . $start->format('Y-m') . '.pdf');
    }
}
