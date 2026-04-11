<?php

namespace App\Models;

/**
 * =====================================================
 * MODEL: ReadingHistory (Riwayat Bacaan)
 * =====================================================
 * Menyimpan riwayat bacaan pengguna yang sudah login.
 * Guest (tanpa login) tidak menyimpan riwayat.
 * =====================================================
 */

use Illuminate\Database\Eloquent\Model;

class ReadingHistory extends Model
{
    public $timestamps = false; // Menggunakan read_at bukan created_at/updated_at

    protected $fillable = [
        'user_id',
        'story_id',
        'chapter_id',
        'read_at',
    ];

    protected function casts(): array
    {
        return [
            'read_at' => 'datetime',
        ];
    }

    // ==========================================
    // Relasi Database
    // ==========================================

    /** Pembaca */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /** Cerita yang dibaca */
    public function story()
    {
        return $this->belongsTo(Story::class);
    }

    /** Chapter yang dibaca */
    public function chapter()
    {
        return $this->belongsTo(Chapter::class);
    }
}
