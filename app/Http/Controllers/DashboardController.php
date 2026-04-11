<?php

namespace App\Http\Controllers;

/**
 * =====================================================
 * CONTROLLER: DashboardController (Dashboard)
 * =====================================================
 * Mengelola halaman dashboard berdasarkan role:
 * - Author: Dashboard lengkap (semua fitur)
 * - Admin: Dashboard lengkap (tanpa kelola admin/author)
 * - Member: Dashboard basic (profil + history)
 * =====================================================
 */

use Illuminate\Http\Request;
use App\Models\Story;
use App\Models\User;
use App\Models\Comment;
use App\Models\StoryRequest;
use App\Models\Notification;

class DashboardController extends Controller
{
    /** Dashboard utama — redirect berdasarkan role */
    public function index()
    {
        $user = auth()->user();

        if ($user->isAuthor()) {
            return $this->authorDashboard();
        } elseif ($user->isAdmin()) {
            return $this->adminDashboard();
        } else {
            return $this->memberDashboard();
        }
    }

    /** Dashboard Author (paling lengkap) */
    private function authorDashboard()
    {
        $user = auth()->user();
        $totalStories = Story::count();
        $totalChapters = \App\Models\Chapter::count();
        $totalComments = Comment::count();
        $totalMembers = User::where('role', 'member')->count();
        $totalAdmins = User::where('role', 'admin')->count();
        $pendingRequests = StoryRequest::where('status', 'pending')->count();
        $notifications = Notification::where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        $recentComments = Comment::with(['user', 'chapter.story'])
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        return view('dashboard.index', compact(
            'user', 'totalStories', 'totalChapters', 'totalComments',
            'totalMembers', 'totalAdmins', 'pendingRequests',
            'notifications', 'recentComments'
        ));
    }

    /** Dashboard Admin */
    private function adminDashboard()
    {
        $user = auth()->user();
        $totalStories = Story::count();
        $totalChapters = \App\Models\Chapter::count();
        $totalComments = Comment::count();
        $totalMembers = User::where('role', 'member')->count();
        $pendingRequests = StoryRequest::where('status', 'pending')->count();
        $notifications = Notification::where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        $recentComments = Comment::with(['user', 'chapter.story'])
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        return view('dashboard.index', compact(
            'user', 'totalStories', 'totalChapters', 'totalComments',
            'totalMembers', 'pendingRequests', 'notifications', 'recentComments'
        ));
    }

    /** Dashboard Member (basic) */
    private function memberDashboard()
    {
        $user = auth()->user();
        $readingHistory = $user->readingHistories()
            ->with(['story', 'chapter'])
            ->orderByDesc('read_at')
            ->limit(20)
            ->get();

        $favorites = $user->favorites()
            ->with('story')
            ->orderByDesc('created_at')
            ->get();

        return view('dashboard.index', compact('user', 'readingHistory', 'favorites'));
    }

    /** Halaman semua komentar (author/admin) */
    public function comments()
    {
        $comments = Comment::with(['user', 'chapter.story'])
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('dashboard.comments', compact('comments'));
    }

    /** Halaman daftar member (author/admin) */
    public function members()
    {
        $members = User::where('role', 'member')
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('dashboard.members', compact('members'));
    }

    /** Halaman notifikasi */
    public function notifications()
    {
        $notifications = Notification::where('user_id', auth()->id())
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('dashboard.notifications', compact('notifications'));
    }

    /** Halaman riwayat bacaan */
    public function history()
    {
        $readingHistory = auth()->user()->readingHistories()
            ->with(['story', 'chapter'])
            ->orderByDesc('read_at')
            ->paginate(20);

        return view('dashboard.history', compact('readingHistory'));
    }
}
