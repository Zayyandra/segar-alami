<?php
namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Roles
        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'owner', 'guard_name' => 'web']);

        // Owner
        $owner = User::firstOrCreate(
            ['email' => 'owner@segaralami.com'],
            ['name' => 'Nofa Maiyuhalmuna', 'password' => Hash::make('password8')]
        );
        $owner->syncRoles(['owner']);

        // Admin
        $admin = User::firstOrCreate(
            ['email' => 'admin@segaralami.com'],
            ['name' => 'Nadia Indira Kirana', 'password' => Hash::make('password8')]
        );
        $admin->syncRoles(['admin']);
    }
}
