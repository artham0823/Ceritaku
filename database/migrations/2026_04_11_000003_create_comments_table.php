<?php

/**
 * =====================================================
 * MIGRATION: Tabel Comments (Komentar)
 * =====================================================
 * Menyimpan komentar pengguna pada setiap chapter.
 * 
 * Sistem Limit Komentar per Hari:
 * - Author (artham) : Unlimited (tanpa batas)
 * - Admin           : Maksimal 10 komentar per hari
 * - Member          : Maksimal 3 komentar per hari
 * - Guest           : Tidak bisa berkomentar
 * 
 * Limit dihitung berdasarkan jumlah komentar hari ini
 * (tidak perlu tabel khusus, cukup query COUNT WHERE date = today)
 * 
 * Hak Hapus Komentar:
 * - Author : Bisa hapus semua komentar
 * - Admin  : Bisa hapus semua komentar (kecuali milik author)
 * - Member : Hanya bisa hapus komentar miliknya sendiri
 * =====================================================
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')                     // Penulis komentar
                  ->constrained('users')
                  ->onDelete('cascade');
            $table->foreignId('chapter_id')                  // Chapter yang dikomentari
                  ->constrained('chapters')
                  ->onDelete('cascade');
            $table->text('content');                          // Isi komentar
            $table->timestamps();                            // created_at & updated_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('comments');
    }
};
