<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BahanBaku;
use App\Models\BahanKeluar;
use App\Models\BahanKeluarBatch;
use App\Models\BahanMasuk;
use App\Models\KonversiProduk;
use App\Models\VarianProduk;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class BahanKeluarController extends Controller
{
    public function index(Request $request): View
    {
        $query = BahanKeluar::with(['bahanBaku', 'varianProduk.produk', 'user', 'pemakaianBatch.bahanMasuk'])
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
        $bahanBakus     = BahanBaku::where('is_active', true)->orderBy('nama')->get();
        $varianProduk   = VarianProduk::with('produk')->where('is_active', true)
            ->orderBy('produk_id')->get();
        $bahanKeluar    = new BahanKeluar();
        $konversiMapJs  = $this->buildKonversiMapJs();

        return view('admin.bahan-keluar.create', compact('bahanBakus', 'varianProduk', 'bahanKeluar', 'konversiMapJs'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'bahan_baku_id'     => 'required|exists:bahan_baku,id',
            'varian_produk_id'  => 'nullable|exists:varian_produk,id',
            'tanggal'           => 'required|date',
            'jumlah'            => 'required|numeric|min:0.01',
            'keterangan'        => 'nullable|string',
        ]);

        $bahanBaku = BahanBaku::findOrFail($validated['bahan_baku_id']);
        if ($bahanBaku->stok_saat_ini < $validated['jumlah']) {
            return back()->withErrors([
                'jumlah' => "Stok tidak cukup. Stok tersedia: {$bahanBaku->stok_saat_ini} {$bahanBaku->satuan}.",
            ])->withInput();
        }

        $validated['user_id'] = auth()->id();
        $warning = null;

        DB::transaction(function () use ($validated, &$warning) {
            $hasilProduksi = $this->hitungDanTerapkanKonversi(
                $validated['bahan_baku_id'],
                $validated['varian_produk_id'] ?? null,
                (float) $validated['jumlah'],
                $warning
            );

            $validated['hasil_produksi'] = $hasilProduksi;

            $bahanKeluar = BahanKeluar::create($validated);

            $this->potongStokFefo($bahanKeluar, $validated['bahan_baku_id'], (float) $validated['jumlah']);

            BahanBaku::where('id', $validated['bahan_baku_id'])
                ->decrement('stok_saat_ini', $validated['jumlah']);
        });

        $message = 'Bahan keluar berhasil dicatat dan stok telah dikurangi (mengikuti urutan FEFO).';
        if ($warning) {
            return redirect()->route('app.bahan-keluar.index')
                ->with('success', $message)
                ->with('warning', $warning);
        }

        return redirect()->route('app.bahan-keluar.index')->with('success', $message);
    }

    public function show(BahanKeluar $bahanKeluar): View
    {
        $bahanKeluar->load(['bahanBaku', 'varianProduk.produk', 'user', 'pemakaianBatch.bahanMasuk']);

        return view('admin.bahan-keluar.show', compact('bahanKeluar'));
    }

    public function edit(BahanKeluar $bahanKeluar): View
    {
        $bahanBakus     = BahanBaku::where('is_active', true)->orderBy('nama')->get();
        $varianProduk   = VarianProduk::with('produk')->where('is_active', true)
            ->orderBy('produk_id')->get();
        $konversiMapJs  = $this->buildKonversiMapJs();

        return view('admin.bahan-keluar.edit', compact('bahanKeluar', 'bahanBakus', 'varianProduk', 'konversiMapJs'));
    }

    public function update(Request $request, BahanKeluar $bahanKeluar): RedirectResponse
    {
        $validated = $request->validate([
            'bahan_baku_id'    => 'required|exists:bahan_baku,id',
            'varian_produk_id' => 'nullable|exists:varian_produk,id',
            'tanggal'          => 'required|date',
            'jumlah'           => 'required|numeric|min:0.01',
            'keterangan'       => 'nullable|string',
        ]);

        $oldBahanBakuId = $bahanKeluar->bahan_baku_id;
        $oldJumlah      = (float) $bahanKeluar->jumlah;
        $newBahanBakuId = (int) $validated['bahan_baku_id'];
        $newJumlah      = (float) $validated['jumlah'];

        $bahanBakuUntukCek = BahanBaku::findOrFail($newBahanBakuId);
        $stokEfektif = $oldBahanBakuId == $newBahanBakuId
            ? $bahanBakuUntukCek->stok_saat_ini + $oldJumlah
            : $bahanBakuUntukCek->stok_saat_ini;

        if ($stokEfektif < $newJumlah) {
            return back()->withErrors([
                'jumlah' => "Stok tidak cukup. Stok tersedia: {$stokEfektif} {$bahanBakuUntukCek->satuan}.",
            ])->withInput();
        }

        $warning = null;

        DB::transaction(function () use ($validated, $bahanKeluar, $oldBahanBakuId, $oldJumlah, $newBahanBakuId, $newJumlah, &$warning) {
            $this->batalkanKonversi($bahanKeluar);
            $this->kembalikanBatchFefo($bahanKeluar);

            if ($oldBahanBakuId != $newBahanBakuId) {
                BahanBaku::where('id', $oldBahanBakuId)->increment('stok_saat_ini', $oldJumlah);
                BahanBaku::where('id', $newBahanBakuId)->decrement('stok_saat_ini', $newJumlah);
            } else {
                $selisih = $newJumlah - $oldJumlah;
                if ($selisih != 0) {
                    BahanBaku::where('id', $newBahanBakuId)->decrement('stok_saat_ini', $selisih);
                }
            }

            $this->potongStokFefo($bahanKeluar, $newBahanBakuId, $newJumlah);

            $hasilProduksi = $this->hitungDanTerapkanKonversi(
                $newBahanBakuId,
                $validated['varian_produk_id'] ?? null,
                $newJumlah,
                $warning
            );
            $validated['hasil_produksi'] = $hasilProduksi;

            $bahanKeluar->update($validated);
        });

        $message = 'Data bahan keluar berhasil diperbarui.';
        if ($warning) {
            return redirect()->route('app.bahan-keluar.index')
                ->with('success', $message)
                ->with('warning', $warning);
        }

        return redirect()->route('app.bahan-keluar.index')->with('success', $message);
    }

    public function destroy(BahanKeluar $bahanKeluar): RedirectResponse
    {
        DB::transaction(function () use ($bahanKeluar) {
            $this->batalkanKonversi($bahanKeluar);
            $this->kembalikanBatchFefo($bahanKeluar);

            BahanBaku::where('id', $bahanKeluar->bahan_baku_id)
                ->increment('stok_saat_ini', $bahanKeluar->jumlah);

            $bahanKeluar->delete();
        });

        return redirect()->route('app.bahan-keluar.index')
            ->with('success', 'Data bahan keluar dihapus dan stok telah dikembalikan ke batch asalnya.');
    }

    private function potongStokFefo(BahanKeluar $bahanKeluar, int $bahanBakuId, float $jumlahDibutuhkan): void
    {
        $batches = BahanMasuk::batchTersediaFefo($bahanBakuId);
        $sisaDibutuhkan = $jumlahDibutuhkan;

        foreach ($batches as $batch) {
            if ($sisaDibutuhkan <= 0) {
                break;
            }

            $diambil = min((float) $batch->sisa_jumlah, $sisaDibutuhkan);

            if ($diambil <= 0) {
                continue;
            }

            BahanMasuk::where('id', $batch->id)->decrement('sisa_jumlah', $diambil);

            BahanKeluarBatch::create([
                'bahan_keluar_id' => $bahanKeluar->id,
                'bahan_masuk_id'  => $batch->id,
                'jumlah_diambil'  => $diambil,
            ]);

            $sisaDibutuhkan -= $diambil;
        }
    }

    private function kembalikanBatchFefo(BahanKeluar $bahanKeluar): void
    {
        $pemakaian = BahanKeluarBatch::where('bahan_keluar_id', $bahanKeluar->id)->get();

        foreach ($pemakaian as $p) {
            BahanMasuk::where('id', $p->bahan_masuk_id)->increment('sisa_jumlah', $p->jumlah_diambil);
        }

        BahanKeluarBatch::where('bahan_keluar_id', $bahanKeluar->id)->delete();
    }

    private function hitungDanTerapkanKonversi(
        int $bahanBakuId,
        ?int $varianProdukId,
        float $jumlahBahanKeluar,
        ?string &$warning
    ): ?float {
        if (!$varianProdukId) {
            return null;
        }

        $konversi = KonversiProduk::where('bahan_baku_id', $bahanBakuId)
            ->where('varian_produk_id', $varianProdukId)
            ->first();

        if (!$konversi || (float) $konversi->jumlah_per_satuan <= 0) {
            $warning = 'Konversi belum diatur untuk kombinasi bahan baku dan varian produk ini. '
                . 'Stok produk tidak diperbarui otomatis — silakan lengkapi data konversi produk.';
            return null;
        }

        $hasilProduksi = floor($jumlahBahanKeluar / (float) $konversi->jumlah_per_satuan);

        VarianProduk::where('id', $varianProdukId)->increment('stok', $hasilProduksi);

        return $hasilProduksi;
    }

    private function batalkanKonversi(BahanKeluar $bahanKeluar): void
    {
        if ($bahanKeluar->varian_produk_id && $bahanKeluar->hasil_produksi) {
            VarianProduk::where('id', $bahanKeluar->varian_produk_id)
                ->decrement('stok', $bahanKeluar->hasil_produksi);
        }
    }

    private function buildKonversiMapJs(): array
    {
        return KonversiProduk::all()
            ->mapWithKeys(fn ($k) => [
                "{$k->bahan_baku_id}_{$k->varian_produk_id}" => (float) $k->jumlah_per_satuan,
            ])
            ->toArray();
    }
}
