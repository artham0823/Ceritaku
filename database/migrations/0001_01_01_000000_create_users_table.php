<?php

/**
 * =====================================================
 * MIGRATION: Tabel Users
 * =====================================================
 * Tabel utama untuk menyimpan data semua pengguna.
 * 
 * Kolom penting:
 * - username  : Username unik untuk login (contoh: artham)
 * - role      : Peran pengguna (author/admin/member)
 * - avatar    : Path foto profil pengguna
 * - title     : Gelar/title (khusus author, misal: "Penulis Utama")
 * - bio       : Deskripsi singkat tentang pengguna
 * - is_blocked: Status blokir akun (true = diblokir)
 * 
 * Sistem Role:
 * - author : Hanya 1 akun (username: artham), otoritas tertinggi
 * - admin  : Dibuat oleh author, bisa kelola konten
 * - member : Bisa dibuat siapa saja, fitur terbatas
 * =====================================================
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');                          // Nama tampilan pengguna
            $table->string('username')->unique();            // Username unik untuk login
            $table->string('password');                      // Password (di-hash otomatis)
            $table->enum('role', ['author', 'admin', 'member'])->default('member'); // Peran pengguna
            $table->string('avatar')->nullable();            // Path foto profil
            $table->string('title')->nullable();             // Gelar (khusus author)
            $table->text('bio')->nullable();                 // Deskripsi singkat
            $table->boolean('is_blocked')->default(false);   // Status blokir
            $table->rememberToken();                         // Token "ingat saya"
            $table->timestamps();                            // created_at & updated_at
        });

        // Tabel untuk reset password (bawaan Laravel)
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        // Tabel session untuk menyimpan data sesi pengguna
        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
