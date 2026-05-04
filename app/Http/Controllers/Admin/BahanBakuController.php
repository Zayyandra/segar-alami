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

        if ($kategori = $request->input('kategori_bb')) {
            $query->where('kategori_bb', $kategori);
        }

        $bahanBakus = $query->paginate(15)->withQueryString();

        $ssRopData = [];
        foreach ($bahanBakus as $item) {
            if ($item->tracking_ss_rop) {
                $ssRopData[$item->id] = $item->hitungSsRop();
            }
        }

        $totalBB     = BahanBaku::where('is_active', true)->count();
        $totalAman   = BahanBaku::where('is_active', true)->whereColumn('stok_saat_ini', '>', 'stok_minimum')->count();
        $totalKritis = BahanBaku::where('is_active', true)->whereColumn('stok_saat_ini', '<=', 'stok_minimum')->count();

        return view('admin.bahan-baku.index', compact(
            'bahanBakus', 'ssRopData', 'totalBB', 'totalAman', 'totalKritis'
        ));
    }

    public function create(): View
    {
        $bahanBaku = new BahanBaku(['is_active' => true, 'tracking_ss_rop' => false]);
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

    public function destroy(BahanBaku $bahanBaku)
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
