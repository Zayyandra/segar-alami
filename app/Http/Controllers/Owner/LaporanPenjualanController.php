<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Penjualan;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
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
        $rataPerHari     = $start->daysInMonth > 0 ? $totalPendapatan / $start->daysInMonth : 0;

        $transaksis = Penjualan::with('user')
            ->whereBetween('tanggal', [$start, $end])
            ->latest('tanggal')
            ->paginate(15)
            ->withQueryString();

        return view('owner.laporan-penjualan', compact(
            'bulan', 'start', 'end', 'totalPendapatan', 'totalTransaksi', 'rataPerHari', 'transaksis'
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
        $rataPerHari     = $start->daysInMonth > 0 ? $totalPendapatan / $start->daysInMonth : 0;

        $transaksis = Penjualan::with('user')
            ->whereBetween('tanggal', [$start, $end])
            ->latest('tanggal')
            ->get();

        $pdf = Pdf::loadView('owner.pdf.laporan-penjualan', compact(
            'start', 'end', 'totalPendapatan', 'totalTransaksi', 'rataPerHari', 'transaksis'
        ))->setPaper('a4', 'portrait');

        return $pdf->download('laporan-penjualan-' . $start->format('Y-m') . '.pdf');
    }
}
