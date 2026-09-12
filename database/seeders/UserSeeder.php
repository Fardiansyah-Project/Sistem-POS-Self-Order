<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin utama
        User::updateOrCreate(
            ['email' => 'admin@korirocoffee.id'],
            [
                'name'     => 'Admin Koriro',
                'email'    => 'admin@korirocoffee.id',
                'password' => bcrypt('admin123'),
                'role'     => 'admin',
            ]
        );

        // Akun kasir
        User::updateOrCreate(
            ['email' => 'kasir@korirocoffee.id'],
            [
                'name'     => 'Kasir Tondo',
                'email'    => 'kasir@korirocoffee.id',
                'password' => bcrypt('kasir123'),
                'role'     => 'kasir',
            ]
        );
    }
}
