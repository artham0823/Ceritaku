<?php

/**
 * =====================================================
 * MIGRATION: Tabel Chapters (Bab/Chapter)
 * =====================================================
 * Menyimpan bab-bab dari setiap cerita.
 * 
 * Kolom penting:
 * - story_id       : ID cerita induk (FK ke stories)
 * - title          : Judul bab (contoh: "Bab 1: Awal Mula")
 * - content        : Isi bab dalam format HTML (mendukung dialog, narasi, dll)
 * - chapter_number : Nomor urut bab untuk pengurutan
 * 
 * Catatan:
 * - Kolom content menggunakan longText karena menyimpan HTML rich content
 * - HTML content bisa berisi class: .dialogue, .narration, .scene-setting, dll
 * - Urutan bab ditentukan oleh chapter_number
 * =====================================================
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chapters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('story_id')                    // Cerita induk
                  ->constrained('stories')
                  ->onDelete('cascade');
            $table->string('title');                          // Judul bab
            $table->longText('content');                      // Isi bab (HTML)
            $table->unsignedInteger('chapter_number');        // Nomor urut bab
            $table->timestamps();                             // created_at & updated_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chapters');
    }
};
