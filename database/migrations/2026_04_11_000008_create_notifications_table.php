<?php

/**
 * =====================================================
 * MIGRATION: Tabel Notifications (Notifikasi)
 * =====================================================
 * Menyimpan notifikasi perubahan data untuk author dan admin.
 * 
 * Jenis notifikasi:
 * - Cerita ditambahkan/diubah/dihapus
 * - Chapter ditambahkan/diubah/dihapus
 * - Komentar baru
 * - Request cerita baru
 * - Akun dibuat/diubah/diblokir
 * 
 * Limit notifikasi:
 * - Author : Maksimal 100 notifikasi (ke-101 otomatis hapus yang terlama)
 * - Admin  : Maksimal 50 notifikasi (ke-51 otomatis hapus yang terlama)
 * - Member : Tidak ada notifikasi
 * 
 * actor_username: Nama/username orang yang melakukan aksi
 * =====================================================
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')                     // Pemilik notifikasi
                  ->constrained('users')
                  ->onDelete('cascade');
            $table->string('type');                           // Jenis notifikasi
            $table->text('message');                          // Pesan notifikasi
            $table->string('actor_username')->nullable();     // Siapa yang melakukan aksi
            $table->timestamp('created_at')->nullable();      // Waktu notifikasi
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
