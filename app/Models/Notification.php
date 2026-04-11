<?php

namespace App\Models;

/**
 * =====================================================
 * MODEL: Notification (Notifikasi)
 * =====================================================
 * Menyimpan notifikasi perubahan data.
 * Auto-cleanup: Author max 100, Admin max 50.
 * =====================================================
 */

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    public $timestamps = false; // Hanya pakai created_at

    protected $fillable = [
        'user_id',
        'type',
        'message',
        'actor_username',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
        ];
    }

    // ==========================================
    // Relasi Database
    // ==========================================

    /** Pemilik notifikasi */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // ==========================================
    // Helper: Buat notifikasi + auto-cleanup
    // ==========================================

    /**
     * Buat notifikasi baru dan hapus yang lama jika melebihi limit.
     * Author: max 100, Admin: max 50
     */
    public static function createForUser(User $user, string $type, string $message, ?string $actorUsername = null): void
    {
        // Hanya untuk author dan admin
        if (!$user->isAuthor() && !$user->isAdmin()) return;

        self::create([
            'user_id' => $user->id,
            'type' => $type,
            'message' => $message,
            'actor_username' => $actorUsername,
            'created_at' => now(),
        ]);

        // Auto-cleanup: hapus notifikasi lama yang melebihi limit
        $limit = $user->isAuthor() ? 100 : 50;
        $count = self::where('user_id', $user->id)->count();

        if ($count > $limit) {
            $deleteCount = $count - $limit;
            $oldIds = self::where('user_id', $user->id)
                ->orderBy('created_at')
                ->limit($deleteCount)
                ->pluck('id');
            self::whereIn('id', $oldIds)->delete();
        }
    }
}
