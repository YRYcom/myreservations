<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;

class RolesAndUsersSeeder extends Seeder
{
    public function run(): void
    {
        // Créer les rôles
        $userRole = Role::firstOrCreate(['name' => 'user']);
        $adminRole = Role::firstOrCreate(['name' => 'admin']);

        // Créer l'utilisateur standard
        $user = User::firstOrCreate(
            ['email' => 'user@myreservations.tld'],
            [
                'name' => 'User',
                'password' => 'password',
            ]
        );
        $user->assignRole($userRole);

        // Créer l'utilisateur admin
        $admin = User::firstOrCreate(
            ['email' => 'admin@myreservations.tld'],
            [
                'name' => 'Admin',
                'password' => 'password',
            ]
        );
        $admin->assignRole($adminRole);
    }
}