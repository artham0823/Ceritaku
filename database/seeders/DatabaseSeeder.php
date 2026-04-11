<?php

namespace Database\Seeders;

/**
 * =====================================================
 * SEEDER: DatabaseSeeder (Utama)
 * =====================================================
 * Menjalankan semua seeder dalam urutan yang benar.
 * 
 * Urutan:
 * 1. UserSeeder   → Buat akun-akun default
 * 2. StorySeeder  → Import data cerita dari ceritaku
 * 3. NavbarSeeder → Buat navigasi default
 * 
 * Cara menjalankan:
 * php artisan migrate:fresh --seed
 * =====================================================
 */

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UserSeeder::class,   // 1. Buat akun
            StorySeeder::class,  // 2. Import cerita
            NavbarSeeder::class, // 3. Buat navbar
        ]);
    }
}
