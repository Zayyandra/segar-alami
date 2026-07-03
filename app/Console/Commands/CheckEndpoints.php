<?php

namespace App\Console\Commands;

use App\Models\BahanBaku;
use App\Models\BahanMasuk;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class CheckEndpoints extends Command
{
    protected $signature   = 'check:endpoints';
    protected $description = 'Pre-flight: cek semua endpoint hidup + simulasi CRUD dummy (auto-rollback). BUKAN pengganti black box manual.';

    private int $pass = 0;
    private int $fail = 0;

    /**
     * Daftar route GET yang dites per role.
     * Untuk route dengan parameter {id}, isi 'param' dengan closure pencari id.
     */
    private function adminGetRoutes(): array
    {
        $firstBahanBaku   = BahanBaku::query()->value('id');
        $firstBahanMasuk  = BahanMasuk::query()->value('id');

        return [
            ['app.dashboard',            []],
            ['app.kategori.index',       []],
            ['app.kategori.create',      []],
            ['app.produk.index',         []],
            ['app.produk.create',        []],
            ['app.varian-produk.index',  []],
            ['app.varian-produk.create', []],
            ['app.bahan-baku.index',     []],
            ['app.bahan-baku.create',    []],
            ['app.bahan-baku.edit',      $firstBahanBaku  ? [$firstBahanBaku]  : null],
            ['app.penjualan.index',      []],
            ['app.penjualan.create',     []],
            ['app.bahan-masuk.index',    []],
            ['app.bahan-masuk.create',   []],
            ['app.bahan-masuk.edit',     $firstBahanMasuk ? [$firstBahanMasuk] : null],
            ['app.bahan-keluar.index',   []],
            ['app.bahan-keluar.create',  []],
            ['app.konversi-produk.index',  []],
            ['app.konversi-produk.create', []],
            ['app.masa-simpan.index',    []],
        ];
    }

    private function ownerGetRoutes(): array
    {
        return [
            ['app.dashboard',              []],
            ['app.laporan.penjualan',      []],
            ['app.laporan.persediaan',     []],
            ['app.users.index',            []],
            ['app.users.create',           []],
        ];
    }

    public function handle(): int
    {
        $this->newLine();
        $this->line('<fg=cyan;options=bold>== SEGAR ALAMI -- PRE-FLIGHT ENDPOINT CHECK ==</>');
        $this->line('<fg=yellow>Catatan: ini alat bantu. Black box di Lampiran D tetap dijalankan manual di browser.</>');

        $admin = $this->findUserWithRole('admin');
        $owner = $this->findUserWithRole('owner');

        if (!$admin) {
            $this->error('Tidak ada user dengan role admin. Hentikan.');
            return self::FAILURE;
        }
        if (!$owner) {
            $this->error('Tidak ada user dengan role owner. Hentikan.');
            return self::FAILURE;
        }

        $this->section("1. Endpoint GET sebagai ADMIN (login: {$admin->email})");
        $this->runGetRoutes($admin, $this->adminGetRoutes(), 200);

        $this->section("2. Endpoint GET sebagai OWNER (login: {$owner->email})");
        $this->runGetRoutes($owner, $this->ownerGetRoutes(), 200);

        $this->section('3. Proteksi Role (harus 403)');
        $this->expectForbidden($admin, 'app.users.index', [], 'Admin akses /app/users');
        $this->expectForbidden($admin, 'app.laporan.penjualan', [], 'Admin akses laporan penjualan');
        $this->expectForbidden($owner, 'app.penjualan.index', [], 'Owner akses penjualan');
        $this->expectForbidden($owner, 'app.bahan-baku.index', [], 'Owner akses bahan baku');

        $this->section('4. Simulasi CRUD Bahan Masuk (DUMMY + AUTO-ROLLBACK)');
        $this->simulateBahanMasukCrud($admin);

        $this->section('5. Export PDF (GET, owner)');
        $this->runGetRoutes($owner, [
            ['app.laporan.penjualan.pdf',  []],
            ['app.laporan.persediaan.pdf', []],
        ], 200);

        $this->summary();
        return $this->fail === 0 ? self::SUCCESS : self::FAILURE;
    }

    // -- CORE: jalankan GET route, cek status --------------------------------

    private function runGetRoutes(User $user, array $routes, int $expected): void
    {
        Auth::login($user);

        foreach ($routes as $entry) {
            [$name, $params] = $entry;

            if ($params === null) {
                $this->check("$name", false, 'SKIP: tidak ada data untuk parameter (DB kosong?)');
                continue;
            }

            try {
                $url      = route($name, $params);
                $response = $this->getInternal($url, $user);
                $status   = $response->getStatusCode();

                // 200 OK, atau 302 redirect (mis. ke login) dianggap "hidup tapi cek manual"
                $ok = $status === $expected;
                $this->check("$name", $ok, $ok ? "status $status OK" : "status $status (diharapkan $expected)");
            } catch (\Throwable $e) {
                $this->check("$name", false, 'EXCEPTION: ' . $this->trimMsg($e->getMessage()));
            }
        }

        Auth::logout();
    }

    private function expectForbidden(User $user, string $name, array $params, string $label): void
    {
        Auth::login($user);
        try {
            $url      = route($name, $params);
            $response = $this->getInternal($url, $user);
            $status   = $response->getStatusCode();
            $ok = $status === 403;
            $this->check($label, $ok, $ok ? '403 (terblokir, benar)' : "status $status (harusnya 403)");
        } catch (\Throwable $e) {
            // Spatie kadang lempar exception, bukan response 403
            $msg = $e->getMessage();
            $ok  = str_contains($msg, '403') || stripos($msg, 'permission') !== false || stripos($msg, 'role') !== false;
            $this->check($label, $ok, $ok ? '403 via exception (benar)' : 'EXCEPTION: ' . $this->trimMsg($msg));
        }
        Auth::logout();
    }

    // -- SIMULASI CRUD: create -> edit -> delete, lalu ROLLBACK ---------------

    private function simulateBahanMasukCrud(User $admin): void
    {
        $bahanBaku = BahanBaku::query()->where('is_active', true)->first();
        if (!$bahanBaku) {
            $this->check('Simulasi CRUD bahan masuk', false, 'SKIP: tidak ada bahan baku aktif');
            return;
        }

        $stokAwal = $bahanBaku->stok_saat_ini;

        // Semua operasi di dalam transaction yang DIPASTIKAN rollback.
        // Data dummy benar-benar dibuat (membuktikan fitur jalan), lalu dibatalkan.
        try {
            DB::beginTransaction();

            // CREATE
            $dummy = BahanMasuk::create([
                'bahan_baku_id'      => $bahanBaku->id,
                'tanggal'            => now()->toDateString(),
                'jumlah'             => 5,
                'lead_time_hari'     => 2,
                'tanggal_kadaluarsa' => now()->addDays(30)->toDateString(),
                'nama_supplier'      => '__TEST__ Otomatis',
                'keterangan'         => '__TEST__ jangan dipakai, akan dirollback',
                'user_id'            => $admin->id,
            ]);
            $bahanBaku->increment('stok_saat_ini', 5);
            $this->check('CREATE bahan masuk dummy', (bool) $dummy->id, "id dummy = {$dummy->id}");

            // EDIT (ubah jumlah 5 -> 8, stok ikut naik 3)
            $dummy->update(['jumlah' => 8]);
            $bahanBaku->increment('stok_saat_ini', 3);
            $cek = BahanMasuk::find($dummy->id);
            $this->check('EDIT bahan masuk dummy', (int) $cek->jumlah === 8, "jumlah sekarang = {$cek->jumlah}");

            // DELETE (stok dikembalikan -8)
            $idDel = $dummy->id;
            $dummy->delete();
            $bahanBaku->decrement('stok_saat_ini', 8);
            $this->check('DELETE bahan masuk dummy', BahanMasuk::find($idDel) === null, 'record dummy terhapus');

            DB::rollBack();

            // Verifikasi stok kembali ke awal setelah rollback
            $stokAkhir = BahanBaku::find($bahanBaku->id)->stok_saat_ini;
            $this->check(
                'ROLLBACK: stok kembali ke nilai awal',
                (float) $stokAkhir === (float) $stokAwal,
                "awal={$stokAwal}, setelah rollback={$stokAkhir}"
            );
        } catch (\Throwable $e) {
            DB::rollBack();
            $this->check('Simulasi CRUD bahan masuk', false, 'EXCEPTION: ' . $this->trimMsg($e->getMessage()));
        }

        // Jaring pengaman: pastikan tidak ada sisa data __TEST__
        $sisa = BahanMasuk::where('nama_supplier', 'like', '__TEST__%')->count();
        $this->check('Tidak ada sisa data __TEST__ di DB', $sisa === 0, "ditemukan {$sisa} record");
    }

    // -- HTTP internal handler (tanpa server, tanpa ngrok) -------------------

    private function getInternal(string $url, User $user)
    {
        $path = parse_url($url, PHP_URL_PATH) ?: '/';
        $query = parse_url($url, PHP_URL_QUERY);
        if ($query) {
            $path .= '?' . $query;
        }

        $request = \Illuminate\Http\Request::create($path, 'GET');
        $request->setUserResolver(fn() => $user);

        return app(\Illuminate\Contracts\Http\Kernel::class)->handle($request);
    }

    // -- HELPERS --------------------------------------------------------------

    private function findUserWithRole(string $role): ?User
    {
        return User::query()
            ->whereHas('roles', fn($q) => $q->where('name', $role))
            ->first();
    }

    private function trimMsg(string $msg): string
    {
        $msg = strtok($msg, "\n");
        return strlen($msg) > 120 ? substr($msg, 0, 117) . '...' : $msg;
    }

    private function check(string $label, bool $ok, string $detail = ''): void
    {
        if ($ok) {
            $this->pass++;
            $icon = '<fg=green>PASS</>';
        } else {
            $this->fail++;
            $icon = '<fg=red>FAIL</>';
        }
        $this->line("  $icon  $label" . ($detail ? "\n        -> $detail" : ''));
    }

    private function section(string $title): void
    {
        $this->newLine();
        $this->line("<fg=cyan;options=bold>== $title ==</>");
    }

    private function summary(): void
    {
        $this->newLine();
        $this->line('==========================================');
        $this->line("<fg=green;options=bold>  PASS: {$this->pass}</>  <fg=red;options=bold>FAIL: {$this->fail}</>");
        $this->line('==========================================');
        $this->newLine();
        $this->line('<fg=magenta>Ingat: hasil ini cuma pre-flight. Untuk Lampiran D, jalankan skenario manual di browser.</>');
        $this->newLine();
    }
}
