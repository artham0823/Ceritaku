<?php

/**
 * =====================================================
 * MIGRATION: Tabel Likes
 * =====================================================
 * Menyimpan data like pada cerita.
 * 
 * Siapa yang bisa like:
 * - Author  : ✅ Bisa (berdasarkan user_id)
 * - Admin   : ✅ Bisa (berdasarkan user_id)
 * - Member  : ✅ Bisa (berdasarkan user_id)
 * - Guest   : ✅ Bisa (berdasarkan ip_address)
 * 
 * Cara kerja:
 * - Jika user sudah login → simpan user_id
 * - Jika guest → simpan ip_address
 * - Satu user/IP hanya bisa like 1 kali per cerita
 * - Like bisa di-toggle (like/unlike)
 * =====================================================
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('likes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('story_id')                    // Cerita yang di-like
                  ->constrained('stories')
                  ->onDelete('cascade');
            $table->foreignId('user_id')                     // User yang like (nullable untuk guest)
                  ->nullable()
                  ->constrained('users')
                  ->onDelete('cascade');
            $table->string('ip_address', 45)->nullable();    // IP address untuk guest
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('likes');
    }
};
