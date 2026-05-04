<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BahanBaku;
use App\Models\BahanMasuk;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BahanMasukController extends Controller
{
    public function index(Request $request): View
    {
        $query = BahanMasuk::with(['bahanBaku', 'user'])
            ->latest('tanggal')->latest('id');

        if ($request->filled('bahan_baku_id')) {
            $query->where('bahan_baku_id', $request->bahan_baku_id);
        }

        if ($request->filled('tanggal')) {
            $query->whereDate('tanggal', $request->tanggal);
        }

        $bahanMasuks = $query->paginate(15)->withQueryString();
        $bahanBakus  = BahanBaku::where('is_active', true)->orderBy('nama')->get();

        return view('admin.bahan-masuk.index', compact('bahanMasuks', 'bahanBakus'));
    }

    public function create(): View
    {
        $bahanBakus = BahanBaku::where('is_active', true)->orderBy('nama')->get();
        $bahanMasuk = new BahanMasuk();

        return view('admin.bahan-masuk.create', compact('bahanBakus', 'bahanMasuk'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'bahan_baku_id'      => 'required|exists:bahan_baku,id',
            'tanggal'            => 'required|date',
            'jumlah'             => 'required|numeric|min:0.01',
            'lead_time_hari'     => 'nullable|integer|min:0',
            'tanggal_kadaluarsa' => 'nullable|date|after:tanggal',
            'nama_supplier'      => 'nullable|string|max:255',
            'keterangan'         => 'nullable|string',
        ]);

        DB::transaction(function () use ($validated) {
            $validated['user_id'] = auth()->id();
            BahanMasuk::create($validated);
            BahanBaku::where('id', $validated['bahan_baku_id'])
                ->increment('stok_saat_ini', $validated['jumlah']);
        });

        return redirect()->route('app.bahan-masuk.index')
            ->with('success', 'Bahan masuk berhasil dicatat dan stok telah diperbarui.');
    }

    public function update(Request $request, BahanMasuk $bahanMasuk): RedirectResponse
    {
        $validated = $request->validate([
            'bahan_baku_id'      => 'required|exists:bahan_baku,id',
            'tanggal'            => 'required|date',
            'jumlah'             => 'required|numeric|min:0.01',
            'lead_time_hari'     => 'nullable|integer|min:0',
            'tanggal_kadaluarsa' => 'nullable|date',
            'nama_supplier'      => 'nullable|string|max:255',
            'keterangan'         => 'nullable|string',
        ]);

        DB::transaction(function () use ($validated, $bahanMasuk) {
            $oldBahanBakuId = $bahanMasuk->bahan_baku_id;
            $oldJumlah      = $bahanMasuk->jumlah;
            $newBahanBakuId = $validated['bahan_baku_id'];
            $newJumlah      = $validated['jumlah'];

            if ($oldBahanBakuId != $newBahanBakuId) {
                BahanBaku::where('id', $oldBahanBakuId)->decrement('stok_saat_ini', $oldJumlah);
                BahanBaku::where('id', $newBahanBakuId)->increment('stok_saat_ini', $newJumlah);
            } else {
                $selisih = $newJumlah - $oldJumlah;
                if ($selisih != 0) {
                    BahanBaku::where('id', $newBahanBakuId)->increment('stok_saat_ini', $selisih);
                }
            }

            $bahanMasuk->update($validated);
        });

        return redirect()->route('app.bahan-masuk.index')
            ->with('success', 'Data bahan masuk berhasil diperbarui.');
    }

    public function destroy(BahanMasuk $bahanMasuk): RedirectResponse
    {
        DB::transaction(function () use ($bahanMasuk) {
            BahanBaku::where('id', $bahanMasuk->bahan_baku_id)
                ->decrement('stok_saat_ini', $bahanMasuk->jumlah);
            $bahanMasuk->delete();
        });

        return redirect()->route('app.bahan-masuk.index')
            ->with('success', 'Data bahan masuk dihapus dan stok telah disesuaikan.');
    }
}
