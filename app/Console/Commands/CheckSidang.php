<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CheckSidang extends Command
{
    protected $signature   = 'check:sidang';
    protected $description = 'Verifikasi sistem Segar Alami sebelum sidang/demo';

    private int $pass = 0;
    private int $fail = 0;
    private int $warnCount = 0;

    public function handle(): void
    {
        $this->newLine();
        $this->line('<fg=cyan;options=bold>╔══════════════════════════════════════════╗</>');
        $this->line('<fg=cyan;options=bold>║   SEGAR ALAMI — PRE-SIDANG SYSTEM CHECK  ║</>');
        $this->line('<fg=cyan;options=bold>╚══════════════════════════════════════════╝</>');

        $this->checkEnv();
        $this->checkRoutes();
        $this->checkControllers();
        $this->checkViews();
        $this->checkEnglishText();
        $this->checkBootstrap();
        $this->checkDatabase();
        $this->summary();
        $this->manualChecklist();
    }

    // ── ENV ──────────────────────────────────────────────────────────────────

    private function checkEnv(): void
    {
        $this->section('1. Konfigurasi .env');

        $env = file_exists(base_path('.env')) ? file_get_contents(base_path('.env')) : '';

        preg_match('/APP_DEBUG\s*=\s*(\w+)/', $env, $m);
        $debug = $m[1] ?? 'tidak ditemukan';
        $this->check(
            'APP_DEBUG=false',
            strtolower($debug) === 'false',
            strtolower($debug) !== 'false' ? "Nilai: APP_DEBUG=$debug  ← GANTI sebelum sidang!" : "Nilai: $debug"
        );

        preg_match('/APP_URL\s*=\s*(.+)/', $env, $m2);
        $url = trim($m2[1] ?? '');
        $this->warnLine("APP_URL saat ini", $url ?: 'kosong — update ke URL ngrok sebelum demo');
    }

    // ── ROUTES ───────────────────────────────────────────────────────────────

    private function checkRoutes(): void
    {
        $this->section('2. Routes');

        $web = $this->readFile(base_path('routes/web.php'));

        $this->check(
            'Route destroy penjualan dihapus',
            !str_contains($web, "->except(['destroy']") === false ||
            str_contains($web, "->except(['destroy']"),
            str_contains($web, "->except(['destroy']") ? "except(['destroy']) ditemukan ✔" : "Periksa manual"
        );

        $this->check(
            'Route safety-stock.index dihapus',
            !str_contains($web, 'safety-stock'),
            str_contains($web, 'safety-stock') ? "Masih ada route safety-stock!" : ''
        );

        $this->check(
            'Middleware role terpasang',
            str_contains($web, 'role:admin') || str_contains($web, 'role:owner') || str_contains($web, "'role'"),
            ''
        );
    }

    // ── CONTROLLERS ──────────────────────────────────────────────────────────

    private function checkControllers(): void
    {
        $this->section('3. Controllers');

        // PenjualanController
        $penjualan = $this->readFile(app_path('Http/Controllers/Admin/PenjualanController.php'));
        $this->check(
            'PenjualanController: method destroy dihapus',
            !str_contains($penjualan, 'function destroy'),
            str_contains($penjualan, 'function destroy') ? "Method destroy masih ada!" : ''
        );
        $this->check(
            'PenjualanController: method edit/update ada',
            str_contains($penjualan, 'function edit') || str_contains($penjualan, 'function update'),
            ''
        );

        // DashboardController
        $dash = $this->readFile(app_path('Http/Controllers/Admin/DashboardController.php'));
        $this->check(
            'DashboardController: produkTerjualBulanIni ada',
            str_contains($dash, 'produkTerjualBulanIni') || str_contains($dash, 'produk_terjual'),
            ''
        );
        $this->check(
            'DashboardController: Cache::remember dihapus dari volumePerKategori',
            !preg_match('/Cache::remember[^;]*volumePerKategori|volumePerKategori[^;]*Cache::remember/s', $dash),
            str_contains($dash, 'Cache::remember') ? "Masih ada Cache::remember — cek apakah di volumePerKategori" : ''
        );

        // BahanBakuController
        $bahan = $this->readFile(app_path('Http/Controllers/Admin/BahanBakuController.php'));
        $this->check(
            'BahanBakuController: kalkulasi SS/ROP ada',
            str_contains($bahan, 'safety_stock') || str_contains($bahan, '$ss') || str_contains($bahan, 'safetyStock'),
            ''
        );

        // LaporanPenjualanController
        $laporan = $this->readFile(app_path('Http/Controllers/Owner/LaporanPenjualanController.php'));
        if (empty($laporan)) {
            $laporan = $this->readFile(app_path('Http/Controllers/Admin/LaporanPenjualanController.php'));
        }
        $this->check(
            'LaporanPenjualanController: $produkTerlaris dihapus',
            !str_contains($laporan, 'produkTerlaris') && !str_contains($laporan, 'produk_terlaris'),
            str_contains($laporan, 'produkTerlaris') ? "Masih ada \$produkTerlaris!" : ''
        );
    }

    // ── VIEWS ─────────────────────────────────────────────────────────────────

    private function checkViews(): void
    {
        $this->section('4. Views — Blade Files');

        $base = resource_path('views');

        // Admin dashboard
        $adminDash = $this->readFile("$base/admin/dashboard.blade.php");
        $this->check(
            'Admin dashboard: produkTerjualBulanIni dirender',
            str_contains($adminDash, 'produkTerjualBulanIni') || str_contains($adminDash, 'produk_terjual'),
            ''
        );

        // Penjualan index
        $penj = $this->readFile("$base/admin/penjualan/index.blade.php");
        $this->check(
            'Penjualan index: form DELETE dihapus',
            !str_contains($penj, "method('DELETE')") && !str_contains($penj, "@method('DELETE')"),
            str_contains($penj, 'DELETE') ? "Masih ada referensi DELETE" : ''
        );
        $this->check(
            'Penjualan index: tombol edit ada',
            str_contains($penj, 'edit') || str_contains($penj, 'Edit'),
            ''
        );

        // Bahan baku
        $bb = $this->readFile("$base/admin/bahan-baku/index.blade.php");
        $this->check(
            'Bahan Baku: kolom Safety Stock tampil',
            str_contains($bb, 'safety_stock') || str_contains($bb, 'Safety Stock') || str_contains($bb, 'safetyStock'),
            ''
        );
        $this->check(
            'Bahan Baku: kolom ROP tampil',
            str_contains($bb, 'ROP') || str_contains($bb, 'rop'),
            ''
        );
        $this->check(
            'Bahan Baku: keterangan rumus ada',
            str_contains($bb, 'SS =') || str_contains($bb, 'ROP =') || str_contains($bb, 'Rumus') || str_contains($bb, 'rumus'),
            ''
        );

        // Laporan penjualan owner
        $lap = $this->readFile("$base/owner/laporan-penjualan.blade.php");
        $this->check(
            'Laporan Penjualan: chart/canvas dihapus',
            !str_contains($lap, '<canvas') && !preg_match('/\bChart\b/', $lap),
            str_contains($lap, 'canvas') ? "Masih ada <canvas>" : ''
        );
        $this->check(
            'Laporan Penjualan: tabel transaksi ada',
            str_contains($lap, '<table') || str_contains($lap, 'transaksi'),
            ''
        );

        // PDF blade
        $pdf = $this->readFile("$base/owner/pdf/laporan-penjualan.blade.php");
        $this->check(
            'PDF blade: $produkTerlaris tidak ada',
            !str_contains($pdf, 'produkTerlaris') && !str_contains($pdf, 'produk_terlaris'),
            str_contains($pdf, 'produkTerlaris') ? "Masih ada \$produkTerlaris di PDF!" : ''
        );

        // Owner dashboard
        $ownerDash = $this->readFile("$base/owner/dashboard.blade.php");
        $this->check(
            "Owner dashboard: teks 'Operational Hub' dihapus",
            !str_contains($ownerDash, 'Operational Hub'),
            str_contains($ownerDash, 'Operational Hub') ? "Masih ada teks 'Operational Hub'" : ''
        );
        $this->check(
            'Owner dashboard: chart laporan tidak ada',
            !(str_contains($ownerDash, 'laporan') && str_contains($ownerDash, '<canvas')),
            ''
        );

        // Error pages
        foreach (['403', '404', '500'] as $code) {
            $err = $this->readFile("$base/errors/{$code}.blade.php");
            $this->check(
                "Error page $code dikustomisasi",
                !empty($err) && strlen($err) > 200,
                empty($err) ? "File tidak ditemukan!" : ''
            );
        }
    }

    // ── SCAN BAHASA INGGRIS ───────────────────────────────────────────────────

    private function checkEnglishText(): void
    {
        $this->section('5. Scan Teks Bahasa Inggris di Views');

        $dir     = resource_path('views');
        $pattern = '/\b(Delete|Submit|Cancel|Back|Search|Filter|Export|Revenue|Add\s+New|Edit\s+Data|Save\s+Changes|Confirm|Close|Loading)\b/';
        $flagged = [];

        $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($dir));
        foreach ($iterator as $file) {
            if ($file->getExtension() !== 'php') continue;
            $content = file_get_contents($file->getPathname());
            if (preg_match($pattern, $content, $m)) {
                $rel       = str_replace(resource_path('views') . DIRECTORY_SEPARATOR, '', $file->getPathname());
                $flagged[] = $rel . ' → "' . $m[0] . '"';
            }
        }

        if (empty($flagged)) {
            $this->check('Tidak ada teks Inggris umum terdeteksi', true, '');
        } else {
            foreach ($flagged as $f) {
                $this->check('Teks Inggris terdeteksi', false, $f);
            }
        }
    }

    // ── BOOTSTRAP ─────────────────────────────────────────────────────────────

    private function checkBootstrap(): void
    {
        $this->section('6. Bootstrap & Middleware');

        $boot = $this->readFile(base_path('bootstrap/app.php'));
        $this->check(
            'Spatie role middleware terdaftar di bootstrap/app.php',
            str_contains($boot, 'RoleMiddleware') || (str_contains($boot, 'role') && str_contains($boot, 'middleware')),
            ''
        );
    }

    // ── DATABASE ──────────────────────────────────────────────────────────────

    private function checkDatabase(): void
    {
        $this->section('7. Database');

        $dbPath = database_path('database.sqlite');
        $exists = file_exists($dbPath);
        $this->check('File SQLite ada', $exists, $exists ? 'database.sqlite ditemukan ✔' : 'Tidak ada!');

        if ($exists) {
            $size = filesize($dbPath);
            $this->check(
                'Database tidak kosong',
                $size > 50000,
                round($size / 1024, 1) . ' KB' . ($size < 50000 ? ' — terlalu kecil, mungkin kosong' : '')
            );
        }
    }

    // ── SUMMARY ───────────────────────────────────────────────────────────────

    private function summary(): void
    {
        $this->newLine();
        $this->line('══════════════════════════════════════════');
        $this->line(
            "<fg=green;options=bold>  PASS: {$this->pass}</>" .
            "  <fg=red;options=bold>FAIL: {$this->fail}</>" .
            "  <fg=yellow;options=bold>WARN: {$this->warnCount}</>"
        );
        $this->line('══════════════════════════════════════════');
    }

    // ── MANUAL CHECKLIST ──────────────────────────────────────────────────────

    private function manualChecklist(): void
    {
        $this->newLine();
        $this->line('<fg=magenta;options=bold>── Checklist Manual (cek di browser) ──</>');
        $items = [
            'Login admin → buka /app/users → harus 403',
            'Login owner → buka /app/penjualan → harus 403',
            'Dashboard admin: kartu pendapatan tampilkan nama bulan berjalan',
            'Dashboard owner: tidak ada chart laporan penjualan',
            'Export PDF laporan penjualan berhasil (tidak 500)',
            'Ada transaksi bulan ini (supaya dashboard tidak Rp 0)',
            'Ada bahan masuk dengan tanggal_kadaluarsa (test notif expired)',
            'APP_URL di .env sudah diganti ke URL ngrok sebelum demo',
        ];
        foreach ($items as $item) {
            $this->line("  <fg=yellow>[ ]</> $item");
        }
        $this->newLine();
    }

    // ── HELPERS ───────────────────────────────────────────────────────────────

    private function check(string $label, bool $ok, string $detail = ''): void
    {
        if ($ok) {
            $this->pass++;
            $icon = '<fg=green>✔ PASS</>';
        } else {
            $this->fail++;
            $icon = '<fg=red>✘ FAIL</>';
        }
        $this->line("  $icon  $label" . ($detail ? "\n         → $detail" : ''));
    }

    private function warnLine(string $label, string $detail = ''): void
    {
        $this->warnCount++;
        $icon = '<fg=yellow>⚠ WARN</>';
        $this->line("  $icon  $label" . ($detail ? "\n         → $detail" : ''));
    }

    private function section(string $title): void
    {
        $this->newLine();
        $this->line("<fg=cyan;options=bold>══ $title ══</>");
    }

    private function readFile(string $path): string
    {
        return file_exists($path) ? file_get_contents($path) : '';
    }
}
