<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RolesAndUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $superadmin = Role::firstOrCreate(['name' => 'superadmin']);
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $researcher = Role::firstOrCreate(['name' => 'researcher']);
        $adminerRole = Role::firstOrCreate(['name' => 'adminer_user']);

        $user1 = User::updateOrCreate(
            ['email' => 'superadmin@yopmail.com'],
            [
                'name' => 'Super Admin',
                'password' => bcrypt('password'),
            ]
        );
        $user1->assignRole($superadmin);

        $user2 = User::updateOrCreate(
            ['email' => 'admin@yopmail.com'],
            [
                'name' => 'Admin User',
                'password' => bcrypt('password'),
            ]
        );
        $user2->assignRole($admin);

        $user3 = User::updateOrCreate(
            ['email' => 'researcher@yopmail.com'],
            [
                'name' => 'Researcher User',
                'password' => bcrypt('password'),
            ]
        );
        $user3->assignRole($researcher);
    }
}
