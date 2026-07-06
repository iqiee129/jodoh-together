<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $password = env('ADMIN_INITIAL_PASSWORD');

        if (! $password) {
            throw new \RuntimeException('ADMIN_INITIAL_PASSWORD must be set before seeding the admin user.');
        }

        $admin = User::updateOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make($password),
            ]
        );

        $admin->forceFill([
            'role' => 'admin',
        ])->save();
    }
}
