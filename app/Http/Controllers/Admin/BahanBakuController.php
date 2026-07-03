<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreBahanBakuRequest;
use App\Http\Requests\Admin\UpdateBahanBakuRequest;
use App\Models\BahanBaku;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BahanBakuController extends Controller
{
    public function index(Request $request): View
    {
        $query = BahanBaku::latest('id');

        if ($search = trim((string) $request->input('q'))) {
            $query->where('nama', 'like', "%{$search}%");
        }

        $kategori = $request->input('kategori_bb', 'utama');
        if ($kategori !== 'semua') {
            $query->where('kategori_bb', $kategori);
        }

        $bahanBakus = $query->paginate(15)->withQueryString();

        $allUtama = BahanBaku::where('is_active', true)->where('kategori_bb', 'utama')->get();

        // Hitung SS/ROP sekaligus: item yang tampil + semua bahan utama (2 query total)
        $ids = $bahanBakus->getCollection()->pluck('id')
            ->merge($allUtama->pluck('id'))
            ->unique();

        $batch  = BahanBaku::ssRopBatch($ids);
        $kosong = ['ss' => null, 'rop' => null, 'D' => null, 'Dmax' => null, 'L' => null, 'Lmax' => null];

        $ssRopData = [];
        foreach ($bahanBakus as $item) {
            $ssRopData[$item->id] = $batch[$item->id] ?? $kosong;
        }

        // Stat cards berdasarkan bahan baku UTAMA, threshold = SS dari rumus
        $totalBB     = $allUtama->count();
        $totalAman   = 0;
        $totalKritis = 0;

        foreach ($allUtama as $bb) {
            $sr = $batch[$bb->id] ?? $kosong;
            if ($sr['ss'] !== null && $bb->stok_saat_ini <= $sr['ss']) {
                $totalKritis++;
            } else {
                $totalAman++;
            }
        }

        return view('admin.bahan-baku.index', compact(
            'bahanBakus', 'ssRopData', 'totalBB', 'totalAman', 'totalKritis', 'kategori'
        ));
    }

    public function create(): View
    {
        $bahanBaku = new BahanBaku(['is_active' => true]);
        return view('admin.bahan-baku.create', compact('bahanBaku'));
    }

    public function store(StoreBahanBakuRequest $request): RedirectResponse
    {
        BahanBaku::create($request->validated());
        return redirect()->route('app.bahan-baku.index')
            ->with('success', 'Bahan baku berhasil ditambahkan.');
    }

    public function edit(BahanBaku $bahanBaku): View
    {
        return view('admin.bahan-baku.edit', compact('bahanBaku'));
    }

    public function update(UpdateBahanBakuRequest $request, BahanBaku $bahanBaku): RedirectResponse
    {
        $bahanBaku->update($request->validated());
        return redirect()->route('app.bahan-baku.index')
            ->with('success', 'Bahan baku berhasil diperbarui.');
    }

    public function destroy(BahanBaku $bahanBaku): RedirectResponse
    {
        if ($bahanBaku->bahanMasuk()->exists() || $bahanBaku->bahanKeluar()->exists()) {
            return redirect()->route('app.bahan-baku.index')
                ->with('error', 'Bahan baku tidak dapat dihapus karena sudah memiliki riwayat transaksi.');
        }

        $bahanBaku->delete();

        return redirect()->route('app.bahan-baku.index')
            ->with('success', 'Bahan baku berhasil dihapus.');
    }
}
