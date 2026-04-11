<?php

namespace App\Http\Controllers;

/**
 * =====================================================
 * CONTROLLER: HomeController (Halaman Publik)
 * =====================================================
 * Mengelola halaman-halaman publik:
 * - Beranda (hero + chapter terbaru + rekomendasi)
 * - Jelajahi (semua cerita + filter genre)
 * - Populer (cerita berdasarkan views terbanyak)
 * - Pencarian
 * =====================================================
 */

use Illuminate\Http\Request;
use App\Models\Story;
use App\Models\NavbarItem;

class HomeController extends Controller
{
    /** Halaman beranda / home */
    public function index()
    {
        // Cerita utama (featured) untuk hero section
        $featuredStory = Story::with(['chapters' => function ($q) {
            $q->orderByDesc('chapter_number')->limit(4);
        }])->featured()->first();

        // Semua cerita untuk rekomendasi (kecuali featured)
        $stories = Story::where('is_featured', false)
            ->withCount('chapters')
            ->orderByDesc('views_count')
            ->get();

        // Genre unik untuk filter
        $genres = $this->getUniqueGenres($stories);

        return view('home', compact('featuredStory', 'stories', 'genres'));
    }

    /** Halaman jelajahi / explore */
    public function explore(Request $request)
    {
        $query = Story::withCount('chapters');

        // Filter berdasarkan genre jika ada
        if ($request->genre && $request->genre !== 'Semua') {
            $query->where('genre', 'like', "%{$request->genre}%");
        }

        $stories = $query->orderByDesc('created_at')->get();
        $genres = $this->getUniqueGenres(Story::all());

        return view('explore', compact('stories', 'genres'));
    }

    /** Halaman populer */
    public function popular()
    {
        $stories = Story::withCount('chapters')
            ->orderByDesc('views_count')
            ->get();

        return view('popular', compact('stories'));
    }

    /** Pencarian cerita */
    public function search(Request $request)
    {
        $keyword = $request->q;
        $stories = collect();

        if ($keyword) {
            $stories = Story::search($keyword)
                ->withCount('chapters')
                ->get();
        }

        return view('search', compact('stories', 'keyword'));
    }

    /** Helper: Ambil genre unik dari semua cerita */
    private function getUniqueGenres($stories): array
    {
        $genres = [];
        foreach ($stories as $story) {
            if ($story->genre) {
                foreach (explode(',', $story->genre) as $g) {
                    $genres[] = trim($g);
                }
            }
        }
        $genres = array_unique($genres);
        sort($genres);
        return $genres;
    }
}
