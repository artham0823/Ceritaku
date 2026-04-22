<?php

/**
 * =====================================================
 * MIGRATION: Tabel Social Links
 * =====================================================
 * Tabel untuk menyimpan link sosial media / game ID
 * yang ditambahkan oleh pengguna ke profil mereka.
 * 
 * Kolom:
 * - user_id    : ID pemilik link sosmed (FK → users)
 * - icon       : Class icon FontAwesome (contoh: "fa-brands fa-instagram")
 * - label      : Nama tampilan (contoh: "Instagram")
 * - value      : Isi/link/ID (contoh: "https://instagram.com/artham_.26")
 * - sort_order : Urutan tampil (default: 0)
 * 
 * Batas per role:
 * - Member : Maksimal 10 item
 * - Admin  : Tanpa batas
 * - Author : Tanpa batas
 * =====================================================
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('social_links', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Hapus otomatis jika user dihapus
            $table->string('icon', 100);       // Class FontAwesome untuk icon
            $table->string('label', 100);      // Nama tampilan (contoh: "Instagram")
            $table->string('value', 255);      // Link / ID / nickname
            $table->integer('sort_order')->default(0); // Urutan tampil
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('social_links');
    }
};
