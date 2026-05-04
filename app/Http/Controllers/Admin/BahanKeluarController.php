<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BahanBaku;
use App\Models\BahanKeluar;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BahanKeluarController extends Controller
{
    public function index(Request $request): View
    {
        $query = BahanKeluar::with(['bahanBaku', 'user'])
            ->latest('tanggal')->latest('id');

        if ($request->filled('bahan_baku_id')) {
            $query->where('bahan_baku_id', $request->bahan_baku_id);
        }

        if ($request->filled('tanggal')) {
            $query->whereDate('tanggal', $request->tanggal);
        }

        $bahanKeluars = $query->paginate(15)->withQueryString();
        $bahanBakus   = BahanBaku::where('is_active', true)->orderBy('nama')->get();

        return view('admin.bahan-keluar.index', compact('bahanKeluars', 'bahanBakus'));
    }

    public function create(): View
    {
        $bahanBakus  = BahanBaku::where('is_active', true)->orderBy('nama')->get();
        $bahanKeluar = new BahanKeluar();

        return view('admin.bahan-keluar.create', compact('bahanBakus', 'bahanKeluar'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'bahan_baku_id' => 'required|exists:bahan_baku,id',
            'tanggal'       => 'required|date',
            'jumlah'        => 'required|numeric|min:0.01',
            'keterangan'    => 'nullable|string',
        ]);

        // Cek stok cukup
        $bahanBaku = BahanBaku::findOrFail($validated['bahan_baku_id']);
        if ($bahanBaku->stok_saat_ini < $validated['jumlah']) {
            return back()->withErrors([
                'jumlah' => "Stok tidak cukup. Stok tersedia: {$bahanBaku->stok_saat_ini} {$bahanBaku->satuan}."
            ])->withInput();
        }

        $validated['user_id'] = auth()->id();

        BahanKeluar::create($validated);

        $bahanBaku->decrement('stok_saat_ini', $validated['jumlah']);

        return redirect()->route('app.bahan-keluar.index')
            ->with('success', 'Bahan keluar berhasil dicatat dan stok telah dikurangi.');
    }

    public function edit(BahanKeluar $bahanKeluar): View
    {
        $bahanBakus = BahanBaku::where('is_active', true)->orderBy('nama')->get();

        return view('admin.bahan-keluar.edit', compact('bahanKeluar', 'bahanBakus'));
    }

    public function update(Request $request, BahanKeluar $bahanKeluar): RedirectResponse
    {
        $validated = $request->validate([
            'bahan_baku_id' => 'required|exists:bahan_baku,id',
            'tanggal'       => 'required|date',
            'jumlah'        => 'required|numeric|min:0.01',
            'keterangan'    => 'nullable|string',
        ]);

        $oldBahanBakuId = $bahanKeluar->bahan_baku_id;
        $oldJumlah      = $bahanKeluar->jumlah;
        $newBahanBakuId = $validated['bahan_baku_id'];
        $newJumlah      = $validated['jumlah'];

        if ($oldBahanBakuId != $newBahanBakuId) {
            // Kembalikan stok bahan baku lama
            BahanBaku::where('id', $oldBahanBakuId)->increment('stok_saat_ini', $oldJumlah);
            // Cek stok bahan baku baru
            $bahanBakuBaru = BahanBaku::findOrFail($newBahanBakuId);
            if ($bahanBakuBaru->stok_saat_ini < $newJumlah) {
                // Rollback
                BahanBaku::where('id', $oldBahanBakuId)->decrement('stok_saat_ini', $oldJumlah);
                return back()->withErrors([
                    'jumlah' => "Stok tidak cukup. Stok tersedia: {$bahanBakuBaru->stok_saat_ini} {$bahanBakuBaru->satuan}."
                ])->withInput();
            }
            BahanBaku::where('id', $newBahanBakuId)->decrement('stok_saat_ini', $newJumlah);
        } else {
            $selisih = $newJumlah - $oldJumlah;
            if ($selisih > 0) {
                $bahanBaku = BahanBaku::findOrFail($newBahanBakuId);
                if ($bahanBaku->stok_saat_ini < $selisih) {
                    return back()->withErrors([
                        'jumlah' => "Stok tidak cukup untuk penambahan ini. Stok tersedia: {$bahanBaku->stok_saat_ini} {$bahanBaku->satuan}."
                    ])->withInput();
                }
            }
            BahanBaku::where('id', $newBahanBakuId)->increment('stok_saat_ini', -$selisih);
        }

        $bahanKeluar->update($validated);

        return redirect()->route('app.bahan-keluar.index')
            ->with('success', 'Data bahan keluar berhasil diperbarui.');
    }

    public function destroy(BahanKeluar $bahanKeluar): RedirectResponse
    {
        // Kembalikan stok
        BahanBaku::where('id', $bahanKeluar->bahan_baku_id)
            ->increment('stok_saat_ini', $bahanKeluar->jumlah);

        $bahanKeluar->delete();

        return redirect()->route('app.bahan-keluar.index')
            ->with('success', 'Data bahan keluar dihapus dan stok telah dikembalikan.');
    }
}
