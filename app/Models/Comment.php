<?php

namespace App\Models;

/**
 * =====================================================
 * MODEL: Comment (Komentar)
 * =====================================================
 * Model untuk mengelola komentar pada chapter.
 * Komentar memiliki limit harian berdasarkan role pengguna.
 * =====================================================
 */

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $fillable = [
        'user_id',
        'chapter_id',
        'content',
    ];

    // ==========================================
    // Relasi Database
    // ==========================================

    /** Penulis komentar */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /** Chapter yang dikomentari */
    public function chapter()
    {
        return $this->belongsTo(Chapter::class);
    }
}
