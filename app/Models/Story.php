<?php

namespace App\Models;

/**
 * =====================================================
 * MODEL: Story (Cerita)
 * =====================================================
 * Model untuk mengelola data cerita.
 * Setiap cerita memiliki banyak chapter (bab).
 * =====================================================
 */

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Story extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'cover_image',
        'genre',
        'views_count',
        'likes_count',
        'is_featured',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'is_featured' => 'boolean',
            'views_count' => 'integer',
            'likes_count' => 'integer',
        ];
    }

    // ==========================================
    // Scopes (Filter Query)
    // ==========================================

    /** Filter cerita yang ditampilkan di hero section */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /** Cari cerita berdasarkan kata kunci judul */
    public function scopeSearch($query, string $keyword)
    {
        return $query->where('title', 'like', "%{$keyword}%")
                     ->orWhere('description', 'like', "%{$keyword}%")
                     ->orWhere('genre', 'like', "%{$keyword}%");
    }

    /** Urutkan berdasarkan popularitas (views terbanyak) */
    public function scopePopular($query)
    {
        return $query->orderByDesc('views_count');
    }

    // ==========================================
    // Relasi Database
    // ==========================================

    /** Chapter-chapter dalam cerita ini */
    public function chapters()
    {
        return $this->hasMany(Chapter::class)->orderBy('chapter_number');
    }

    /** Pembuat cerita */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /** Daftar favorit pada cerita ini */
    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }

    /** Daftar like pada cerita ini */
    public function likes()
    {
        return $this->hasMany(Like::class);
    }

    /** Riwayat bacaan cerita ini */
    public function readingHistories()
    {
        return $this->hasMany(ReadingHistory::class);
    }
}
