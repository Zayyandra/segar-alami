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

        $query = BahanMasuk::with('bahanBaku')
            ->whereNotNull('tanggal_kadaluarsa')
            ->orderBy('tanggal_kadaluarsa', 'asc');

        $items = $query->get()->map(function ($item) {
            $sisa = today()->diffInDays($item->tanggal_kadaluarsa, false);
            if ($sisa < 0) {
                $item->status = 'kadaluarsa';
                $item->status_label = 'Sudah Kedaluwarsa';
                $item->sisa_hari = abs($sisa) . ' hari lalu';
            } elseif ($sisa <= 7) {
                $item->status = 'mendekati';
                $item->status_label = 'Mendekati Kedaluwarsa';
                $item->sisa_hari = $sisa . ' hari lagi';
            } else {
                $item->status = 'aman';
                $item->status_label = 'Aman';
                $item->sisa_hari = $sisa . ' hari lagi';
            }
            return $item;
        });

        if ($status) {
            $items = $items->filter(fn($i) => $i->status === $status)->values();
        }

        $counts = [
            'semua'    => $items->count(),
            'kadaluarsa' => $items->where('status', 'kadaluarsa')->count(),
            'mendekati'  => $items->where('status', 'mendekati')->count(),
            'aman'       => $items->where('status', 'aman')->count(),
        ];

        return view('admin.masa-simpan.index', compact('items', 'status', 'counts'));
    }
}
