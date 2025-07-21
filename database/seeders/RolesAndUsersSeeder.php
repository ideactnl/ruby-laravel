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
        $superadmin = Role::firstOrCreate(['name' => 'Superadmin']);
        $admin = Role::firstOrCreate(['name' => 'Admin']);
        $researcher = Role::firstOrCreate(['name' => 'Researcher']);

        $user1 = User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@yopmail.com',
            'password' => bcrypt('password'),
        ]);
        $user1->assignRole($superadmin);

        $user2 = User::create([
            'name' => 'Admin User',
            'email' => 'admin@yopmail.com',
            'password' => bcrypt('password'),
        ]);
        $user2->assignRole($admin);

        $user3 = User::create([
            'name' => 'Researcher User',
            'email' => 'researcher@yopmail.com',
            'password' => bcrypt('password'),
        ]);
        $user3->assignRole($researcher);
    }
}
