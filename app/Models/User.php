<?php

namespace App\Models;

/**
 * =====================================================
 * MODEL: User (Pengguna)
 * =====================================================
 * Model untuk mengelola data pengguna.
 * 
 * Tiga jenis role:
 * - author : Username 'artham', otoritas tertinggi
 * - admin  : Dibuat oleh author, bisa kelola konten
 * - member : Register sendiri, fitur terbatas
 * =====================================================
 */

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'username',
        'password',
        'role',
        'avatar',
        'title',
        'bio',
        'is_blocked',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'is_blocked' => 'boolean',
        ];
    }

    // ==========================================
    // Cek Role
    // ==========================================

    /** Cek apakah user adalah author (artham) */
    public function isAuthor(): bool
    {
        return $this->role === 'author';
    }

    /** Cek apakah user adalah admin */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /** Cek apakah user adalah member */
    public function isMember(): bool
    {
        return $this->role === 'member';
    }

    /** Cek apakah user bisa mengelola konten (author atau admin) */
    public function canManageContent(): bool
    {
        return $this->isAuthor() || $this->isAdmin();
    }

    // ==========================================
    // Gamifikasi & Leveling
    // ==========================================

    /** Dapatkan Level pembaca berdasarkan total riwayat baca */
    public function getLevelName(): string
    {
        if ($this->isAuthor() || $this->isAdmin()) {
            return 'Pustakawan Royal';
        }

        $count = $this->readingHistories()->count();
        if ($count >= 50) return 'Perpustakaan Berjalan';
        if ($count >= 20) return 'Kutu Buku';
        return 'Pembaca Baru';
    }

    /** Dapatkan batas harian komentar berdasarkan level */
    public function getCommentLimit(): int
    {
        if ($this->isAdmin()) return 10;
        
        $level = $this->getLevelName();
        if ($level === 'Perpustakaan Berjalan') return 7;
        if ($level === 'Kutu Buku') return 5;
        
        return 3;
    }

    // ==========================================
    // Limit Komentar per Hari
    // ==========================================

    /** 
     * Cek apakah user masih bisa berkomentar hari ini
     * Author: unlimited, Admin: 10/hari, Member: bervariasi tergantung level
     */
    public function canComment(): bool
    {
        if ($this->isAuthor()) return true;

        $todayCount = $this->comments()
            ->whereDate('created_at', today())
            ->count();

        return $todayCount < $this->getCommentLimit();
    }

    /** Hitung sisa komentar hari ini */
    public function remainingComments(): int|string
    {
        if ($this->isAuthor()) return '∞';

        $todayCount = $this->comments()
            ->whereDate('created_at', today())
            ->count();

        return max(0, $this->getCommentLimit() - $todayCount);
    }

    // ==========================================
    // Relasi Database
    // ==========================================

    /** Cerita yang dibuat oleh user ini */
    public function stories()
    {
        return $this->hasMany(Story::class, 'created_by');
    }

    /** Komentar yang ditulis oleh user ini */
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    /** Riwayat bacaan user ini */
    public function readingHistories()
    {
        return $this->hasMany(ReadingHistory::class);
    }

    /** Cerita favorit user ini */
    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }

    /** Cerita yang di-like user ini */
    public function likes()
    {
        return $this->hasMany(Like::class);
    }

    /** Request cerita oleh user ini */
    public function storyRequests()
    {
        return $this->hasMany(StoryRequest::class);
    }

    /** Notifikasi untuk user ini */
    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    /** Link sosial media / game ID user ini */
    public function socialLinks()
    {
        return $this->hasMany(SocialLink::class)->orderBy('sort_order');
    }

    /**
     * Batas maksimal link sosmed yang bisa ditambahkan.
     * Member: 10, Admin & Author: tanpa batas (unlimited).
     */
    public function getSocialLinksLimit(): int
    {
        if ($this->isAuthor() || $this->isAdmin()) {
            return PHP_INT_MAX; // Tanpa batas
        }
        return 10; // Member max 10
    }
}
