<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Bikin akun admin
        $admin = User::updateOrCreate(
            ['email' => 'admin@pandara.com'], // Cek apakah email ini sudah ada
            [
                'name' => 'Administrator',
                'password' => Hash::make('password123'),
                'role' => 'admin' // Hapus baris ini kalau kamu pakai package Spatie Permission
            ]
        );

        // JIKA KAMU PAKAI SPATIE PERMISSION, hapus '//' di bawah ini:
        // $admin->assignRole('admin');
    }
}