<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChapterReaction extends Model
{
    protected $fillable = ['chapter_id', 'user_id', 'reaction_type'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function chapter()
    {
        return $this->belongsTo(Chapter::class);
    }
}
