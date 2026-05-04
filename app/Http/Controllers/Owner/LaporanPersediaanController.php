<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\BahanBaku;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LaporanPersediaanController extends Controller
{
    public function index(Request $request): View
    {
        $query = BahanBaku::where('is_active', true);

        if ($search = trim((string) $request->input('q'))) {
            $query->where('nama', 'like', "%{$search}%");
        }

        if ($status = $request->input('status')) {
            if ($status === 'aman') {
                $query->whereRaw('stok_saat_ini > stok_minimum');
            } elseif ($status === 'kritis') {
                $query->whereRaw('stok_saat_ini <= stok_minimum AND stok_minimum > 0');
            }
        }

        $bahanBakus  = $query->orderBy('nama')->paginate(15)->withQueryString();
        $all         = BahanBaku::where('is_active', true)->get();
        $totalJenis  = $all->count();
        $jumlahAman  = $all->filter(fn ($b) => $b->stok_minimum > 0 && $b->stok_saat_ini > $b->stok_minimum)->count();
        $jumlahKritis= $all->filter(fn ($b) => $b->stok_minimum > 0 && $b->stok_saat_ini <= $b->stok_minimum)->count();
        $totalNilai  = $all->sum(fn ($b) => $b->stok_saat_ini * $b->harga_per_satuan);

        return view('owner.laporan-persediaan', compact(
            'bahanBakus', 'totalJenis', 'jumlahAman', 'jumlahKritis', 'totalNilai'
        ));
    }

    public function exportPdf()
    {
        $bahanBakus  = BahanBaku::where('is_active', true)->orderBy('nama')->get();
        $totalJenis  = $bahanBakus->count();
        $jumlahAman  = $bahanBakus->filter(fn ($b) => $b->stok_minimum > 0 && $b->stok_saat_ini > $b->stok_minimum)->count();
        $jumlahKritis= $bahanBakus->filter(fn ($b) => $b->stok_minimum > 0 && $b->stok_saat_ini <= $b->stok_minimum)->count();
        $totalNilai  = $bahanBakus->sum(fn ($b) => $b->stok_saat_ini * $b->harga_per_satuan);

        $pdf = Pdf::loadView('owner.pdf.laporan-persediaan', compact(
            'bahanBakus', 'totalJenis', 'jumlahAman', 'jumlahKritis', 'totalNilai'
        ))->setPaper('a4', 'portrait');

        return $pdf->download('laporan-persediaan-' . now()->format('Y-m-d') . '.pdf');
    }
}
