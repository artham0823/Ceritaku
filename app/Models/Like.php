<?php

namespace App\Models;

/**
 * =====================================================
 * MODEL: Like
 * =====================================================
 * Menyimpan data like pada cerita.
 * Semua pengguna termasuk guest bisa like.
 * Guest di-track berdasarkan IP address.
 * =====================================================
 */

use Illuminate\Database\Eloquent\Model;

class Like extends Model
{
    protected $fillable = [
        'story_id',
        'user_id',
        'ip_address',
    ];

    // ==========================================
    // Relasi Database
    // ==========================================

    /** Cerita yang di-like */
    public function story()
    {
        return $this->belongsTo(Story::class);
    }

    /** User yang like (nullable untuk guest) */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
