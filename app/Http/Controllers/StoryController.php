<?php

namespace App\Http\Controllers;

/**
 * =====================================================
 * CONTROLLER: StoryController (CRUD Cerita)
 * =====================================================
 * Mengelola cerita:
 * - Tampilkan detail cerita (publik)
 * - Tambah, edit, hapus cerita (author/admin via dashboard)
 * - Upload cover cerita
 * 
 * Hak akses CRUD: Author dan Admin
 * Hak akses baca: Semua (termasuk guest)
 * =====================================================
 */

use Illuminate\Http\Request;
use App\Models\Story;
use App\Models\Notification;
use App\Models\User;

class StoryController extends Controller
{
    /** Tampilkan detail cerita (publik) */
    public function show($id)
    {
        $story = Story::with(['chapters', 'creator'])->findOrFail($id);
        
        // Tambah view count (setiap kali diakses)
        $story->increment('views_count');

        // Cek apakah user sudah login dan sudah like/favorit
        $isLiked = false;
        $isFavorited = false;
        $readProgress = 0;
        if (auth()->check()) {
            $isLiked = $story->likes()->where('user_id', auth()->id())->exists();
            $isFavorited = $story->favorites()->where('user_id', auth()->id())->exists();
            
            if ($story->chapters->count() > 0) {
                $readChapters = \App\Models\ReadingHistory::where('user_id', auth()->id())
                    ->where('story_id', $story->id)
                    ->distinct('chapter_id')
                    ->count('chapter_id');
                $readProgress = round(($readChapters / $story->chapters->count()) * 100);
            }
        } else {
            $isLiked = $story->likes()->where('ip_address', request()->ip())->exists();
        }

        return view('story.show', compact('story', 'isLiked', 'isFavorited', 'readProgress'));
    }

    /** Daftar cerita di dashboard (author/admin) */
    public function index()
    {
        $stories = Story::withCount('chapters')
            ->orderByDesc('updated_at')
            ->get();

        return view('dashboard.stories.index', compact('stories'));
    }

    /** Form tambah cerita baru */
    public function create()
    {
        return view('dashboard.stories.create');
    }

    /** Simpan cerita baru */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'genre' => 'nullable|string|max:255',
            'cover_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'is_featured' => 'nullable|boolean',
        ], [
            'title.required' => 'Judul cerita wajib diisi.',
            'cover_image.image' => 'File harus berupa gambar.',
            'cover_image.max' => 'Ukuran gambar maksimal 5MB.',
        ]);

        $coverPath = 'img/p2.jpg'; // Default cover
        if ($request->hasFile('cover_image')) {
            $file = $request->file('cover_image');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('img/covers'), $filename);
            $coverPath = 'img/covers/' . $filename;
        }

        $story = Story::create([
            'title' => $request->title,
            'description' => $request->description,
            'cover_image' => $coverPath,
            'genre' => $request->genre,
            'is_featured' => $request->boolean('is_featured'),
            'created_by' => auth()->id(),
        ]);

        // Kirim notifikasi ke author
        $author = User::where('role', 'author')->first();
        if ($author) {
            Notification::createForUser(
                $author,
                'cerita_baru',
                "Cerita baru '{$story->title}' telah ditambahkan.",
                auth()->user()->username
            );
        }

        return redirect()->route('dashboard.stories.index')
            ->with('success', "Cerita '{$story->title}' berhasil ditambahkan!");
    }

    /** Form edit cerita */
    public function edit($id)
    {
        $story = Story::findOrFail($id);
        return view('dashboard.stories.edit', compact('story'));
    }

    /** Update cerita */
    public function update(Request $request, $id)
    {
        $story = Story::findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'genre' => 'nullable|string|max:255',
            'cover_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'is_featured' => 'nullable|boolean',
        ]);

        if ($request->hasFile('cover_image')) {
            $file = $request->file('cover_image');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('img/covers'), $filename);
            $story->cover_image = 'img/covers/' . $filename;
        }

        $story->update([
            'title' => $request->title,
            'description' => $request->description,
            'genre' => $request->genre,
            'cover_image' => $story->cover_image,
            'is_featured' => $request->boolean('is_featured'),
        ]);

        // Notifikasi
        $author = User::where('role', 'author')->first();
        if ($author) {
            Notification::createForUser(
                $author,
                'cerita_diubah',
                "Cerita '{$story->title}' telah diubah.",
                auth()->user()->username
            );
        }

        return redirect()->route('dashboard.stories.index')
            ->with('success', "Cerita '{$story->title}' berhasil diubah!");
    }

    /** Hapus cerita */
    public function destroy($id)
    {
        $story = Story::findOrFail($id);
        $title = $story->title;
        $story->delete();

        // Notifikasi
        $author = User::where('role', 'author')->first();
        if ($author) {
            Notification::createForUser(
                $author,
                'cerita_dihapus',
                "Cerita '{$title}' telah dihapus.",
                auth()->user()->username
            );
        }

        return redirect()->route('dashboard.stories.index')
            ->with('success', "Cerita '{$title}' berhasil dihapus!");
    }
}
