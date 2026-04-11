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

        // Chart Data: Cerita berdasarkan Genre
        $genreStats = Story::selectRaw('genre, count(*) as count')->groupBy('genre')->pluck('count', 'genre')->toArray();
        $genreLabels = array_keys($genreStats);
        $genreData = array_values($genreStats);

        // Chart Data: Aktivitas Baca (7 hari terakhir)
        $readingStats = \App\Models\ReadingHistory::selectRaw('DATE(read_at) as date, count(*) as count')
            ->groupBy('date')
            ->orderBy('date', 'desc')
            ->limit(7)
            ->pluck('count', 'date')
            ->toArray();
        $readingStats = array_reverse($readingStats); // Urut dari terlama ke terbaru dalam 7 hari
        $readingLabels = array_keys($readingStats);
        $readingData = array_values($readingStats);

        return view('dashboard.index', compact(
            'user', 'totalStories', 'totalChapters', 'totalComments',
            'totalMembers', 'totalAdmins', 'pendingRequests',
            'notifications', 'recentComments',
            'genreLabels', 'genreData', 'readingLabels', 'readingData'
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

        $totalStoriesRead = $user->readingHistories()->distinct('story_id')->count('story_id');

        $latestIds = $user->readingHistories()
            ->selectRaw('MAX(id) as id')
            ->groupBy('story_id');

        $readingHistory = \App\Models\ReadingHistory::whereIn('id', $latestIds)
            ->with(['story', 'chapter'])
            ->orderByDesc('read_at')
            ->limit(6)
            ->get();

        $favorites = $user->favorites()
            ->with('story')
            ->orderByDesc('created_at')
            ->get();

        $userComments = $user->comments()->with('chapter.story')->orderByDesc('created_at')->limit(5)->get();
        $userRequests = $user->storyRequests()->orderByDesc('created_at')->limit(5)->get();

        return view('dashboard.index', compact('user', 'totalStoriesRead', 'readingHistory', 'favorites', 'userComments', 'userRequests'));
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
        $user = auth()->user();
        $latestIds = $user->readingHistories()
            ->selectRaw('MAX(id) as id')
            ->groupBy('story_id');

        $readingHistory = \App\Models\ReadingHistory::whereIn('id', $latestIds)
            ->with(['story', 'chapter'])
            ->orderByDesc('read_at')
            ->paginate(20);

        return view('dashboard.history', compact('readingHistory'));
    }
}
