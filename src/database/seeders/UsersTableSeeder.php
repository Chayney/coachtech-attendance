<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $admin = User::create([
            'name' => 'admin',
            'email' => 'admin@admin.com',
            'email_verified_at' => now(),
            'password' => bcrypt('password'),
        ]);
        $user = User::create([
            'name' => 'test',
            'email' => 'test@example.com',
            'email_verified_at' => now(),
            'password' => bcrypt('password')
        ]);
        $adminRole = Role::create(['name' => 'admin']);
        $userRole = Role::create(['name' => 'user']);
        Permission::create(['name' => 'edit']);
        Permission::create(['name' => 'update']);
        $adminRole->givePermissionTo(['edit', 'update']);
        $admin->assignRole($adminRole);
        $user->assignRole($userRole);
    }
}
