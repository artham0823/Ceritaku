<?php

/**
 * =====================================================
 * MIGRATION: Tabel Favorites (Cerita Favorit)
 * =====================================================
 * Menyimpan daftar cerita favorit pengguna.
 * 
 * Siapa yang bisa favorit:
 * - Author  : ✅ Bisa
 * - Admin   : ✅ Bisa
 * - Member  : ✅ Bisa
 * - Guest   : ❌ Tidak bisa (harus login)
 * 
 * Satu pengguna hanya bisa memfavoritkan satu cerita sekali
 * (unique constraint pada user_id + story_id)
 * =====================================================
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('favorites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')                     // Pengguna
                  ->constrained('users')
                  ->onDelete('cascade');
            $table->foreignId('story_id')                    // Cerita yang difavoritkan
                  ->constrained('stories')
                  ->onDelete('cascade');
            $table->timestamps();

            // Pastikan 1 user hanya bisa favorit 1 cerita sekali
            $table->unique(['user_id', 'story_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('favorites');
    }
};
