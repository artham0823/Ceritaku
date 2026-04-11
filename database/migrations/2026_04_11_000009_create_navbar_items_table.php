<?php

/**
 * =====================================================
 * MIGRATION: Tabel Navbar Items (Item Navigasi)
 * =====================================================
 * Menyimpan item-item navigasi yang bisa dikustomisasi.
 * 
 * Hanya author yang bisa:
 * - Menambahkan navbar baru
 * - Mengubah navbar yang ada
 * - Menghapus navbar
 * 
 * Kolom penting:
 * - label      : Teks yang ditampilkan (contoh: "Beranda")
 * - url        : URL tujuan (contoh: "/" atau "/explore")
 * - icon       : Kelas icon FontAwesome (contoh: "fa-solid fa-house")
 * - sort_order : Urutan tampil (angka kecil = tampil duluan)
 * - is_active  : Apakah navbar ini ditampilkan
 * =====================================================
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('navbar_items', function (Blueprint $table) {
            $table->id();
            $table->string('label');                          // Teks navigasi
            $table->string('url');                             // URL tujuan
            $table->string('icon')->nullable();                // Icon FontAwesome
            $table->unsignedInteger('sort_order')->default(0); // Urutan tampil
            $table->boolean('is_active')->default(true);       // Aktif/nonaktif
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('navbar_items');
    }
};
