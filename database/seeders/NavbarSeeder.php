<?php

namespace Database\Seeders;

/**
 * =====================================================
 * SEEDER: NavbarSeeder
 * =====================================================
 * Membuat item navigasi default:
 * - Beranda (/)
 * - Jelajahi (/explore)
 * - Populer (/popular)
 * =====================================================
 */

use Illuminate\Database\Seeder;
use App\Models\NavbarItem;

class NavbarSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            [
                'label' => 'Beranda',
                'url' => '/',
                'icon' => 'fa-solid fa-house',
                'sort_order' => 1,
            ],
            [
                'label' => 'Jelajahi',
                'url' => '/explore',
                'icon' => 'fa-solid fa-compass',
                'sort_order' => 2,
            ],
            [
                'label' => 'Populer',
                'url' => '/popular',
                'icon' => 'fa-solid fa-fire',
                'sort_order' => 3,
            ],
        ];

        foreach ($items as $item) {
            NavbarItem::create($item);
        }

        $this->command->info("✅ Navbar default berhasil dibuat!");
    }
}
