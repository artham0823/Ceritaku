<?php

namespace App\Models;

/**
 * =====================================================
 * MODEL: SocialLink (Link Sosial Media)
 * =====================================================
 * Menyimpan link sosmed / game ID yang ditambahkan
 * oleh user ke profil mereka.
 * 
 * Setiap user bisa punya beberapa link sosmed:
 * - Member : Maksimal 10 item
 * - Admin  : Tanpa batas
 * - Author : Tanpa batas
 * =====================================================
 */

use Illuminate\Database\Eloquent\Model;

class SocialLink extends Model
{
    protected $fillable = [
        'user_id',
        'icon',
        'label',
        'value',
        'sort_order',
    ];

    /**
     * Relasi: link ini milik user tertentu.
     * Setiap SocialLink pasti dimiliki oleh satu User.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
