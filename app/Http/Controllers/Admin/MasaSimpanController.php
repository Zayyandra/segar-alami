<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BahanMasuk;
use Illuminate\Http\Request;

class MasaSimpanController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status');

        $semua = BahanMasuk::with('bahanBaku')
            ->whereNotNull('tanggal_kadaluarsa')
            ->orderBy('tanggal_kadaluarsa', 'asc')
            ->get()
            ->map(function ($item) {
                $sisa = today()->diffInDays($item->tanggal_kadaluarsa, false);
                if ($sisa < 0) {
                    $item->status       = 'kadaluarsa';
                    $item->status_label = 'Sudah Kedaluwarsa';
                    $item->sisa_hari    = abs($sisa) . ' hari lalu';
                } elseif ($sisa <= 7) {
                    $item->status       = 'mendekati';
                    $item->status_label = 'Mendekati Kedaluwarsa';
                    $item->sisa_hari    = $sisa . ' hari lagi';
                } else {
                    $item->status       = 'aman';
                    $item->status_label = 'Aman';
                    $item->sisa_hari    = $sisa . ' hari lagi';
                }
                return $item;
            });

        // Counts dihitung dari data LENGKAP (sebelum filter),
        // supaya stat card tetap benar saat filter aktif
        $counts = [
            'semua'      => $semua->count(),
            'kadaluarsa' => $semua->where('status', 'kadaluarsa')->count(),
            'mendekati'  => $semua->where('status', 'mendekati')->count(),
            'aman'       => $semua->where('status', 'aman')->count(),
        ];

        $items = $status
            ? $semua->filter(fn ($i) => $i->status === $status)->values()
            : $semua;

        return view('admin.masa-simpan.index', compact('items', 'status', 'counts'));
    }
}
