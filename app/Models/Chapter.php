<?php

namespace App\Models;

/**
 * =====================================================
 * MODEL: Chapter (Bab)
 * =====================================================
 * Model untuk mengelola bab-bab cerita.
 * Setiap chapter berisi konten HTML rich text
 * yang mendukung dialog, narasi, dan scene setting.
 * =====================================================
 */

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Chapter extends Model
{
    use HasFactory;

    protected $fillable = [
        'story_id',
        'title',
        'content',
        'chapter_number',
    ];

    // ==========================================
    // Relasi Database
    // ==========================================

    /** Cerita induk dari chapter ini */
    public function story()
    {
        return $this->belongsTo(Story::class);
    }

    /** Komentar pada chapter ini */
    public function comments()
    {
        return $this->hasMany(Comment::class)->orderByDesc('created_at');
    }

    /** Riwayat bacaan chapter ini */
    public function readingHistories()
    {
        return $this->hasMany(ReadingHistory::class);
    }

    // ==========================================
    // Helper Methods
    // ==========================================

    /** Ambil teks biasa (tanpa HTML) untuk preview */
    public function getPlainTextAttribute(): string
    {
        return strip_tags($this->content);
    }

    /** Ambil preview singkat (150 karakter) */
    public function getPreviewAttribute(): string
    {
        return mb_substr($this->plain_text, 0, 150) . '...';
    }
}
