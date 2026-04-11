<?php

/**
 * =====================================================
 * MIGRATION: Tabel Reading Histories (Riwayat Bacaan)
 * =====================================================
 * Menyimpan riwayat bacaan pengguna yang sudah login.
 * 
 * Catatan:
 * - Hanya menyimpan untuk pengguna yang sudah login
 * - Guest (tanpa login) tidak menyimpan riwayat
 * - Setiap kali membaca chapter, record baru dibuat
 * - read_at menyimpan waktu terakhir membaca
 * =====================================================
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reading_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')                     // Pembaca
                  ->constrained('users')
                  ->onDelete('cascade');
            $table->foreignId('story_id')                    // Cerita yang dibaca
                  ->constrained('stories')
                  ->onDelete('cascade');
            $table->foreignId('chapter_id')                  // Chapter yang dibaca
                  ->constrained('chapters')
                  ->onDelete('cascade');
            $table->timestamp('read_at');                     // Waktu membaca
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reading_histories');
    }
};
