<?php

/**
 * =====================================================
 * MIGRATION: Tabel Story Requests (Permintaan Cerita)
 * =====================================================
 * Menyimpan permintaan cerita dari pengguna.
 * 
 * Siapa yang bisa request:
 * - Author  : ✅ Bisa
 * - Admin   : ✅ Bisa
 * - Member  : ✅ Bisa
 * - Guest   : ❌ Harus login/register dulu
 * 
 * Status request:
 * - pending  : Belum diproses
 * - approved : Disetujui
 * - rejected : Ditolak
 * 
 * Form request terletak di bagian footer atau atas footer.
 * =====================================================
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('story_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')                     // Pengguna yang request
                  ->constrained('users')
                  ->onDelete('cascade');
            $table->string('title');                          // Judul cerita yang diminta
            $table->text('description')->nullable();          // Deskripsi/alasan request
            $table->enum('status', ['pending', 'approved', 'rejected'])
                  ->default('pending');                       // Status request
            $table->timestamps();                            // created_at & updated_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('story_requests');
    }
};
