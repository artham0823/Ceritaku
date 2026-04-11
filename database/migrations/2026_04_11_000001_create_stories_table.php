<?php

/**
 * =====================================================
 * MIGRATION: Tabel Stories (Cerita)
 * =====================================================
 * Menyimpan data cerita utama.
 * 
 * Kolom penting:
 * - title       : Judul cerita
 * - description : Deskripsi singkat cerita
 * - cover_image : Path gambar cover cerita
 * - genre       : Genre cerita (bisa lebih dari 1, pisah koma)
 * - views_count : Jumlah pembaca (bertambah setiap kali dibaca)
 * - likes_count : Jumlah like
 * - is_featured : Apakah ditampilkan di hero section
 * - created_by  : ID pengguna yang membuat cerita
 * =====================================================
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stories', function (Blueprint $table) {
            $table->id();
            $table->string('title');                         // Judul cerita
            $table->text('description')->nullable();         // Deskripsi singkat
            $table->string('cover_image')->nullable();       // Path gambar cover
            $table->string('genre')->nullable();             // Genre (pisah koma: "Fantasy, Action")
            $table->unsignedBigInteger('views_count')->default(0);  // Jumlah viewer
            $table->unsignedBigInteger('likes_count')->default(0);  // Jumlah like
            $table->boolean('is_featured')->default(false);  // Tampil di hero section
            $table->foreignId('created_by')                  // Pembuat cerita
                  ->constrained('users')
                  ->onDelete('cascade');
            $table->timestamps();                            // created_at & updated_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stories');
    }
};
