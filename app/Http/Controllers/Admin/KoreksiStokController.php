<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BahanBaku;
use App\Models\KoreksiStok;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class KoreksiStokController extends Controller
{
    public function create(BahanBaku $bahanBaku): View
    {
        return view('admin.koreksi-stok.create', compact('bahanBaku'));
    }

    public function store(Request $request, BahanBaku $bahanBaku): RedirectResponse
    {
        $validated = $request->validate([
            'stok_sesudah' => 'required|numeric|min:0',
            'alasan'       => 'required|in:' . implode(',', array_keys(KoreksiStok::ALASAN_OPTIONS)),
            'keterangan'   => 'nullable|string|max:500',
        ]);

        $stokSebelum = (float) $bahanBaku->stok_saat_ini;
        $stokSesudah = (float) $validated['stok_sesudah'];

        if ($stokSebelum == $stokSesudah) {
            return back()->withErrors([
                'stok_sesudah' => 'Stok sesudah harus berbeda dari stok saat ini (' . $stokSebelum . ' ' . $bahanBaku->satuan . ').',
            ])->withInput();
        }

        DB::transaction(function () use ($bahanBaku, $stokSebelum, $stokSesudah, $validated) {
            KoreksiStok::create([
                'bahan_baku_id' => $bahanBaku->id,
                'user_id'       => auth()->id(),
                'stok_sebelum'  => $stokSebelum,
                'stok_sesudah'  => $stokSesudah,
                'selisih'       => $stokSesudah - $stokSebelum,
                'alasan'        => $validated['alasan'],
                'keterangan'    => $validated['keterangan'] ?? null,
            ]);

            $bahanBaku->update(['stok_saat_ini' => $stokSesudah]);
        });

        return redirect()->route('app.bahan-baku.index')
            ->with('success', "Stok {$bahanBaku->nama} berhasil dikoreksi dari {$stokSebelum} menjadi {$stokSesudah} {$bahanBaku->satuan}.");
    }

    public function index(BahanBaku $bahanBaku): View
    {
        $riwayat = $bahanBaku->koreksiStok()->with('user')->latest()->paginate(15);

        return view('admin.koreksi-stok.index', compact('bahanBaku', 'riwayat'));
    }
}
