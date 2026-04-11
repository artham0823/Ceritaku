<?php

namespace App\Http\Controllers;

/**
 * =====================================================
 * CONTROLLER: LikeController (Like & Favorit)
 * =====================================================
 * Mengelola like dan favorit cerita.
 * 
 * Like: Semua termasuk guest (via IP)
 * Favorit: Author, Admin, Member (harus login)
 * =====================================================
 */

use Illuminate\Http\Request;
use App\Models\Like;
use App\Models\Favorite;
use App\Models\Story;

class LikeController extends Controller
{
    /** Toggle like cerita */
    public function toggleLike(Request $request, $storyId)
    {
        $story = Story::findOrFail($storyId);

        if (auth()->check()) {
            // User sudah login — cek berdasarkan user_id
            $like = Like::where('story_id', $storyId)
                ->where('user_id', auth()->id())
                ->first();

            if ($like) {
                $like->delete();
                $story->decrement('likes_count');
                return back()->with('success', 'Like dibatalkan.');
            } else {
                Like::create([
                    'story_id' => $storyId,
                    'user_id' => auth()->id(),
                ]);
                $story->increment('likes_count');
                return back()->with('success', 'Cerita di-like!');
            }
        } else {
            // Guest — cek berdasarkan IP
            $ip = $request->ip();
            $like = Like::where('story_id', $storyId)
                ->whereNull('user_id')
                ->where('ip_address', $ip)
                ->first();

            if ($like) {
                $like->delete();
                $story->decrement('likes_count');
                return back()->with('success', 'Like dibatalkan.');
            } else {
                Like::create([
                    'story_id' => $storyId,
                    'ip_address' => $ip,
                ]);
                $story->increment('likes_count');
                return back()->with('success', 'Cerita di-like!');
            }
        }
    }

    /** Toggle favorit cerita (harus login) */
    public function toggleFavorite($storyId)
    {
        $story = Story::findOrFail($storyId);

        $favorite = Favorite::where('story_id', $storyId)
            ->where('user_id', auth()->id())
            ->first();

        if ($favorite) {
            $favorite->delete();
            return back()->with('success', 'Cerita dihapus dari favorit.');
        } else {
            Favorite::create([
                'story_id' => $storyId,
                'user_id' => auth()->id(),
            ]);
            return back()->with('success', 'Cerita ditambahkan ke favorit!');
        }
    }
}
