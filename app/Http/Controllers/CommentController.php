<?php

namespace App\Http\Controllers;

/**
 * =====================================================
 * CONTROLLER: CommentController (Komentar)
 * =====================================================
 * Mengelola komentar pada chapter.
 * 
 * Limit komentar per hari:
 * - Author: Unlimited
 * - Admin: 10 per hari
 * - Member: 3 per hari
 * - Guest: Tidak bisa berkomentar
 * 
 * Hak hapus:
 * - Author: Hapus semua komentar
 * - Admin: Hapus semua komentar kecuali milik author
 * - Member: Hapus komentar miliknya sendiri saja
 * =====================================================
 */

use Illuminate\Http\Request;
use App\Models\Comment;
use App\Models\Notification;
use App\Models\User;

class CommentController extends Controller
{
    /** Tambah komentar baru */
    public function store(Request $request)
    {
        $request->validate([
            'chapter_id' => 'required|exists:chapters,id',
            'content' => 'required|string|max:1000',
        ], [
            'content.required' => 'Isi komentar wajib diisi.',
            'content.max' => 'Komentar maksimal 1000 karakter.',
        ]);

        // Cek limit komentar harian
        if (!auth()->user()->canComment()) {
            $remaining = auth()->user()->remainingComments();
            return back()->with('error', "Anda sudah mencapai batas komentar hari ini. Sisa: {$remaining}");
        }

        $comment = Comment::create([
            'user_id' => auth()->id(),
            'chapter_id' => $request->chapter_id,
            'content' => $request->content,
        ]);

        // Notifikasi ke author
        $author = User::where('role', 'author')->first();
        if ($author) {
            Notification::createForUser(
                $author,
                'komentar_baru',
                auth()->user()->name . " berkomentar: \"" . mb_substr($request->content, 0, 50) . "...\"",
                auth()->user()->username
            );
        }

        return back()->with('success', 'Komentar berhasil ditambahkan!');
    }

    /** Hapus komentar */
    public function destroy($id)
    {
        $comment = Comment::findOrFail($id);
        $user = auth()->user();

        // Cek hak hapus
        if ($user->isAuthor()) {
            // Author bisa hapus semua
        } elseif ($user->isAdmin()) {
            // Admin bisa hapus semua kecuali milik author
            if ($comment->user && $comment->user->isAuthor()) {
                return back()->with('error', 'Anda tidak bisa menghapus komentar milik author.');
            }
        } elseif ($user->isMember()) {
            // Member hanya bisa hapus miliknya sendiri
            if ($comment->user_id !== $user->id) {
                return back()->with('error', 'Anda hanya bisa menghapus komentar Anda sendiri.');
            }
        }

        $comment->delete();
        return back()->with('success', 'Komentar berhasil dihapus.');
    }
}
