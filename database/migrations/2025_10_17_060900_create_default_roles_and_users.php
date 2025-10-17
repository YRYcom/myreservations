<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;

return new class extends Migration
{
    public function up(): void
    {
        $faker = Faker::create();

        // Créer les rôles
        $userRole = Role::firstOrCreate(['name' => 'user']);
        $adminRole = Role::firstOrCreate(['name' => 'admin']);

        // Créer un utilisateur standard avec Faker
        $user = User::firstOrCreate(
            ['email' => $faker->unique()->safeEmail()],
            [
                'name' => $faker->name(),
                'password' => Hash::make('password'), // tu peux faker aussi si tu veux
            ]
        );
        $user->assignRole($userRole);

        // Créer un admin avec Faker
        $admin = User::firstOrCreate(
            ['email' => $faker->unique()->safeEmail()],
            [
                'name' => $faker->name(),
                'password' => Hash::make('password'),
            ]
        );
        $admin->assignRole($adminRole);
    }

    public function down(): void
    {
        User::whereHas('roles', function ($q) {
            $q->whereIn('name', ['user', 'admin']);
        })->delete();

        Role::whereIn('name', ['user', 'admin'])->delete();
    }
};