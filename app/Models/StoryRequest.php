<?php

namespace App\Models;

/**
 * =====================================================
 * MODEL: StoryRequest (Permintaan Cerita)
 * =====================================================
 * Menyimpan permintaan cerita dari pengguna.
 * Status: pending, approved, rejected
 * =====================================================
 */

use Illuminate\Database\Eloquent\Model;

class StoryRequest extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'description',
        'status',
    ];

    // ==========================================
    // Relasi Database
    // ==========================================

    /** Pengguna yang meminta cerita */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
