<?php

namespace Database\Seeders;

/**
 * =====================================================
 * SEEDER: UserSeeder
 * =====================================================
 * Membuat akun-akun default:
 * 1. Author (artham) - password: artham0823
 * 2. Admin contoh - password: admin123
 * 3. 2 Member contoh - password: member123
 * =====================================================
 */

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Akun Author (otoritas tertinggi, hanya 1)
        User::create([
            'name' => 'Artham',
            'username' => 'artham',
            'password' => 'artham0823',
            'role' => 'author',
            'title' => 'Penulis Utama',
            'bio' => 'Penulis cerita digital yang terinspirasi dari kehidupan sehari-hari.',
            'avatar' => 'img/p2.jpg',
        ]);

        // Akun Admin contoh
        User::create([
            'name' => 'Admin Satu',
            'username' => 'admin1',
            'password' => 'admin123',
            'role' => 'admin',
            'bio' => 'Administrator platform Ceritaku.',
        ]);

        // Akun Member contoh
        User::create([
            'name' => 'Pembaca Setia',
            'username' => 'pembaca1',
            'password' => 'member123',
            'role' => 'member',
            'bio' => 'Suka membaca cerita fiksi.',
        ]);

        User::create([
            'name' => 'Penggemar Cerita',
            'username' => 'pembaca2',
            'password' => 'member123',
            'role' => 'member',
            'bio' => 'Hobi membaca sejak kecil.',
        ]);
    }
}
