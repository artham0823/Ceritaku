<?php

namespace App\Http\Controllers;

/**
 * =====================================================
 * CONTROLLER: ChapterController (CRUD Bab/Chapter)
 * =====================================================
 * Mengelola bab-bab cerita:
 * - Baca chapter (publik, + view count + history)
 * - Tambah, edit, hapus chapter (author/admin)
 * 
 * Chapter content mendukung HTML rich text:
 * - Dialog (.dialogue)
 * - Narasi (.narration)
 * - Scene setting (.scene-setting)
 * - Character intro (.character-intro)
 * =====================================================
 */

use Illuminate\Http\Request;
use App\Models\Story;
use App\Models\Chapter;
use App\Models\ReadingHistory;
use App\Models\Notification;
use App\Models\User;

class ChapterController extends Controller
{
    /** Baca chapter (halaman reader) */
    public function show($storyId, $chapterId)
    {
        $story = Story::with('chapters')->findOrFail($storyId);
        $chapter = Chapter::where('story_id', $storyId)->findOrFail($chapterId);

        // Tambah view count cerita
        $story->increment('views_count');

        // Simpan riwayat bacaan jika sudah login
        if (auth()->check()) {
            ReadingHistory::create([
                'user_id' => auth()->id(),
                'story_id' => $storyId,
                'chapter_id' => $chapterId,
                'read_at' => now(),
            ]);
        }

        // Ambil chapter sebelumnya dan selanjutnya
        $prevChapter = Chapter::where('story_id', $storyId)
            ->where('chapter_number', '<', $chapter->chapter_number)
            ->orderByDesc('chapter_number')
            ->first();

        $nextChapter = Chapter::where('story_id', $storyId)
            ->where('chapter_number', '>', $chapter->chapter_number)
            ->orderBy('chapter_number')
            ->first();

        // Ambil komentar untuk chapter ini
        $comments = $chapter->comments()->with('user')->orderByDesc('created_at')->get();

        return view('story.reader', compact('story', 'chapter', 'prevChapter', 'nextChapter', 'comments'));
    }

    /** Form tambah chapter baru */
    public function create($storyId)
    {
        $story = Story::findOrFail($storyId);
        $nextNumber = $story->chapters()->max('chapter_number') + 1;
        return view('dashboard.chapters.create', compact('story', 'nextNumber'));
    }

    /** Simpan chapter baru */
    public function store(Request $request, $storyId)
    {
        $story = Story::findOrFail($storyId);

        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'chapter_number' => 'required|integer|min:1',
        ], [
            'title.required' => 'Judul bab wajib diisi.',
            'content.required' => 'Isi bab wajib diisi.',
        ]);

        $chapter = Chapter::create([
            'story_id' => $story->id,
            'title' => $request->title,
            'content' => $request->content,
            'chapter_number' => $request->chapter_number,
        ]);

        // Notifikasi
        $author = User::where('role', 'author')->first();
        if ($author) {
            Notification::createForUser(
                $author,
                'chapter_baru',
                "Bab baru '{$chapter->title}' ditambahkan ke cerita '{$story->title}'.",
                auth()->user()->username
            );
        }

        return redirect()->route('dashboard.stories.index')
            ->with('success', "Bab '{$chapter->title}' berhasil ditambahkan ke '{$story->title}'!");
    }

    /** Form edit chapter */
    public function edit($id)
    {
        $chapter = Chapter::with('story')->findOrFail($id);
        return view('dashboard.chapters.edit', compact('chapter'));
    }

    /** Update chapter */
    public function update(Request $request, $id)
    {
        $chapter = Chapter::with('story')->findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $chapter->update([
            'title' => $request->title,
            'content' => $request->content,
        ]);

        // Notifikasi
        $author = User::where('role', 'author')->first();
        if ($author) {
            Notification::createForUser(
                $author,
                'chapter_diubah',
                "Bab '{$chapter->title}' dari cerita '{$chapter->story->title}' telah diubah.",
                auth()->user()->username
            );
        }

        return redirect()->route('dashboard.stories.index')
            ->with('success', "Bab '{$chapter->title}' berhasil diubah!");
    }

    /** Hapus chapter */
    public function destroy($id)
    {
        $chapter = Chapter::with('story')->findOrFail($id);
        $title = $chapter->title;
        $storyTitle = $chapter->story->title;
        $chapter->delete();

        // Notifikasi
        $author = User::where('role', 'author')->first();
        if ($author) {
            Notification::createForUser(
                $author,
                'chapter_dihapus',
                "Bab '{$title}' dari cerita '{$storyTitle}' telah dihapus.",
                auth()->user()->username
            );
        }

        return redirect()->route('dashboard.stories.index')
            ->with('success', "Bab '{$title}' berhasil dihapus!");
    }
}
