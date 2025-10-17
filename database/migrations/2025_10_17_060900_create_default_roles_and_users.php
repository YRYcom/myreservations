<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

return new class extends Migration
{
    public function up(): void
    {
        
        $userRole = Role::firstOrCreate(['name' => 'user']);
        $adminRole = Role::firstOrCreate(['name' => 'admin']);

        $user = User::firstOrCreate(
            ['email' => 'user@myreservations.tld'],
            [
                'name' => 'User',
                'password' => Hash::make('password'),
            ]
        );
        $user->assignRole($userRole);

        $admin = User::firstOrCreate(
            ['email' => 'admin@myreservations.tld'],
            [
                'name' => 'Admin',
                'password' => Hash::make('password'),
            ]
        );
        $admin->assignRole($adminRole);
    }

    public function down(): void
    {
        $user = User::where('email', 'user@myreservations.tld')->first();
        $admin = User::where('email', 'admin@myreservations.tld')->first();

        if ($user) {
            $user->removeRole('user');
            $user->delete();
        }

        if ($admin) {
            $admin->removeRole('admin');
            $admin->delete();
        }

        Role::whereIn('name', ['user', 'admin'])->delete();
    }
};