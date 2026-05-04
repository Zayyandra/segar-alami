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
            ['email' => 'owner@segar.test'],
            ['name' => 'Owner Segar Alami', 'password' => Hash::make('password')]
        );
        $owner->syncRoles(['owner']);

        // Admin
        $admin = User::firstOrCreate(
            ['email' => 'admin@segar.test'],
            ['name' => 'Admin Segar Alami', 'password' => Hash::make('password')]
        );
        $admin->syncRoles(['admin']);
    }
}
