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
        $all    = BahanBaku::where('is_active', true)->orderBy('nama')->get();
        $allIds = $all->pluck('id')->toArray();
        $batch  = BahanBaku::ssRopBatch($allIds);
        $kosong = ['ss' => null, 'rop' => null];

        // Hitung stat card pakai SS (sama dengan admin)
        $totalJenis   = $all->count();
        $jumlahAman   = $all->filter(fn($b) => ($batch[$b->id] ?? $kosong)['ss'] !== null && $b->stok_saat_ini > ($batch[$b->id]['ss']))->count();
        $jumlahKritis = $all->filter(fn($b) => ($batch[$b->id] ?? $kosong)['ss'] !== null && $b->stok_saat_ini <= ($batch[$b->id]['ss']))->count();
        $totalNilai   = $all->sum(fn($b) => $b->stok_saat_ini * $b->harga_per_satuan);

        // Filter status pakai SS juga
        // Filter status pakai SS juga
        $search   = trim((string) $request->input('q'));
        $status   = $request->input('status');
        $kategori = $request->input('kategori', 'utama'); // ← default 'utama'

        $filtered = $all
            ->when($search, fn($c) => $c->filter(fn($b) => str_contains(strtolower($b->nama), strtolower($search))))
            ->when($kategori, fn($c) => $c->filter(fn($b) => $b->kategori_bb === $kategori)) // ← filter kategori
            ->when($status === 'aman', fn($c) => $c->filter(fn($b) => ($batch[$b->id] ?? $kosong)['ss'] !== null && $b->stok_saat_ini > $batch[$b->id]['ss']))
            ->when($status === 'kritis', fn($c) => $c->filter(fn($b) => ($batch[$b->id] ?? $kosong)['ss'] !== null && $b->stok_saat_ini <= $batch[$b->id]['ss']))
            ->values();

        // Manual paginate
        $page       = $request->input('page', 1);
        $perPage    = 15;
        $bahanBakus = new \Illuminate\Pagination\LengthAwarePaginator(
            $filtered->slice(($page - 1) * $perPage, $perPage)->values(),
            $filtered->count(), $perPage, $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('owner.laporan-persediaan', compact(
            'bahanBakus', 'batch', 'kategori', 'totalJenis', 'jumlahAman', 'jumlahKritis', 'totalNilai'
        ));
    }

    public function exportPdf()
    {
        $bahanBakus = BahanBaku::where('is_active', true)->orderBy('nama')->get();
        $allIds     = $bahanBakus->pluck('id')->toArray();
        $batch      = BahanBaku::ssRopBatch($allIds);
        $kosong     = ['ss' => null, 'rop' => null];

        $totalJenis   = $bahanBakus->count();
        $jumlahAman   = $bahanBakus->filter(fn($b) => ($batch[$b->id] ?? $kosong)['ss'] !== null && $b->stok_saat_ini > $batch[$b->id]['ss'])->count();
        $jumlahKritis = $bahanBakus->filter(fn($b) => ($batch[$b->id] ?? $kosong)['ss'] !== null && $b->stok_saat_ini <= $batch[$b->id]['ss'])->count();
        $totalNilai   = $bahanBakus->sum(fn($b) => $b->stok_saat_ini * $b->harga_per_satuan);

        $pdf = Pdf::loadView('owner.pdf.laporan-persediaan', compact(
            'bahanBakus', 'batch', 'totalJenis', 'jumlahAman', 'jumlahKritis', 'totalNilai'
        ))->setPaper('a4', 'portrait');

        return $pdf->download('laporan-persediaan-' . now()->format('Y-m-d') . '.pdf');
    }
}
