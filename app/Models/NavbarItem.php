<?php

namespace App\Models;

/**
 * =====================================================
 * MODEL: NavbarItem (Item Navigasi)
 * =====================================================
 * Menyimpan item navigasi yang bisa dikustomisasi oleh author.
 * Urutan berdasarkan sort_order (kecil = tampil duluan).
 * =====================================================
 */

use Illuminate\Database\Eloquent\Model;

class NavbarItem extends Model
{
    protected $fillable = [
        'label',
        'url',
        'icon',
        'sort_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    /** Scope: hanya yang aktif, urut berdasarkan sort_order */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('sort_order');
    }
}
