<?php

namespace App\Models;

/**
 * =====================================================
 * MODEL: Favorite (Cerita Favorit)
 * =====================================================
 * Menyimpan cerita favorit pengguna.
 * Author, Admin, dan Member bisa memfavoritkan cerita.
 * =====================================================
 */

use Illuminate\Database\Eloquent\Model;

class Favorite extends Model
{
    protected $fillable = [
        'user_id',
        'story_id',
    ];

    // ==========================================
    // Relasi Database
    // ==========================================

    /** Pengguna yang memfavoritkan */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /** Cerita yang difavoritkan */
    public function story()
    {
        return $this->belongsTo(Story::class);
    }
}
