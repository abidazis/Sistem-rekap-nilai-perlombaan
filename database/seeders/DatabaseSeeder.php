<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Bikin akun admin utama (login memakai username)
        $admin = User::updateOrCreate(
            ['username' => 'admin'],
            [
                'name' => 'Administrator',
                'email' => 'admin@pandara.com',
                'password' => Hash::make('password123'),
                'role' => 'admin'
            ]
        );

        // JIKA KAMU PAKAI SPATIE PERMISSION, hapus '//' di bawah ini:
        // $admin->assignRole('admin');
    }
}