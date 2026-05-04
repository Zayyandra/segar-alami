<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BahanBaku;
use Illuminate\View\View;

class SafetyStockController extends Controller
{
    public function index(): View
    {
        $bahanBakus = BahanBaku::where('is_active', true)
            ->where('tracking_ss_rop', true)
            ->get()
            ->map(function ($bb) {
                $sr = $bb->hitungSsRop();
                $bb->ss        = $sr['ss'];
                $bb->rop       = $sr['rop'];
                $bb->D         = $sr['D'];
                $bb->Dmax      = $sr['Dmax'];
                $bb->L         = $sr['L'];
                $bb->Lmax      = $sr['Lmax'];
                $bb->perlu_reorder = $sr['rop'] !== null && $bb->stok_saat_ini <= $sr['rop'];
                return $bb;
            });

        return view('admin.safety-stock.index', compact('bahanBakus'));
    }
}
