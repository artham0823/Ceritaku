@extends('layouts.dashboard')
@section('title', 'Dashboard - Ceritaku')

@section('content')
<div class="dash-header">
    <div>
        <h1>Selamat Datang, {{ $user->name }}!</h1>
        <p>{{ $user->isAuthor() ? 'Dashboard Author — Otoritas Tertinggi' : ($user->isAdmin() ? 'Dashboard Admin' : 'Dashboard Member') }}</p>
    </div>
    <a href="{{ route('home') }}" class="btn btn-outline"><i class="fa-solid fa-home"></i> Ke Beranda</a>
</div>

{{-- Statistik (Author & Admin) --}}
@if($user->canManageContent())
<div class="stats-grid {{ $user->isAuthor() ? 'author-dashboard' : 'admin-dashboard' }}">
    <div class="stat-card">
        <div class="stat-icon"><i class="fa-solid fa-book"></i></div>
        <div class="stat-value">{{ $totalStories }}</div>
        <div class="stat-label">Total Cerita</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon"><i class="fa-solid fa-file-lines"></i></div>
        <div class="stat-value">{{ $totalChapters }}</div>
        <div class="stat-label">Total Bab</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon"><i class="fa-solid fa-comments"></i></div>
        <div class="stat-value">{{ $totalComments }}</div>
        <div class="stat-label">Total Komentar</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon"><i class="fa-solid fa-users"></i></div>
        <div class="stat-value">{{ $totalMembers }}</div>
        <div class="stat-label">Total Member</div>
    </div>
    @if($user->isAuthor())
    <div class="stat-card">
        <div class="stat-icon"><i class="fa-solid fa-user-shield"></i></div>
        <div class="stat-value">{{ $totalAdmins }}</div>
        <div class="stat-label">Total Admin</div>
    </div>
    @endif
    <div class="stat-card">
        <div class="stat-icon"><i class="fa-solid fa-inbox"></i></div>
        <div class="stat-value">{{ $pendingRequests }}</div>
        <div class="stat-label">Request Pending</div>
    </div>
</div>

{{-- Komentar Terbaru --}}
<div class="dash-card">
    <h3><i class="fa-solid fa-comments"></i> Komentar Terbaru</h3>
    @forelse($recentComments as $comment)
        <div class="comment-item" style="margin-bottom:0.8rem">
            <img src="{{ asset($comment->user->avatar ?? 'img/p2.jpg') }}" alt="" class="comment-avatar">
            <div class="comment-body">
                <div class="comment-header">
                    <span class="comment-name">{{ $comment->user->name }}</span>
                    <span class="comment-date">{{ $comment->created_at->diffForHumans() }}</span>
                </div>
                <p class="comment-text">{{ Str::limit($comment->content, 100) }}</p>
                <small style="color:var(--text-muted)">Pada: {{ $comment->chapter->story->title ?? '' }} — {{ $comment->chapter->title ?? '' }}</small>
            </div>
        </div>
    @empty
        <p style="color:var(--text-muted)">Belum ada komentar.</p>
    @endforelse
</div>

{{-- Notifikasi Terbaru --}}
@if(isset($notifications))
<div class="dash-card">
    <h3><i class="fa-solid fa-bell"></i> Notifikasi Terbaru</h3>
    @forelse($notifications as $notif)
        <div style="padding:0.6rem 0;border-bottom:1px solid var(--border-color);font-size:0.95rem">
            <span>{{ $notif->message }}</span>
            <small style="color:var(--text-muted);display:block;margin-top:0.2rem">
                {{ $notif->actor_username ? 'oleh ' . $notif->actor_username : '' }} — {{ $notif->created_at ? $notif->created_at->diffForHumans() : '' }}
            </small>
        </div>
    @empty
        <p style="color:var(--text-muted)">Belum ada notifikasi.</p>
    @endforelse
    @if($notifications->count() > 0)
        <a href="{{ route('dashboard.notifications') }}" style="display:block;margin-top:1rem;color:var(--primary-color);font-weight:600;font-size:0.9rem">Lihat semua notifikasi →</a>
    @endif
</div>
@endif

@else
{{-- Dashboard Member --}}
<div class="member-dashboard">
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon"><i class="fa-solid fa-clock-rotate-left"></i></div>
            <div class="stat-value">{{ $readingHistory->count() }}</div>
            <div class="stat-label">Cerita Dibaca</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon"><i class="fa-solid fa-bookmark"></i></div>
            <div class="stat-value">{{ $favorites->count() }}</div>
            <div class="stat-label">Cerita Favorit</div>
        </div>
    </div>

    <div class="dash-card">
        <h3><i class="fa-solid fa-bookmark"></i> Cerita Favorit</h3>
        <div class="favorites-grid">
            @forelse($favorites as $fav)
                <a href="{{ route('story.show', $fav->story->id) }}" class="story-card" style="text-decoration:none;color:inherit">
                    <div class="story-cover">
                        <img src="{{ asset($fav->story->cover_image ?? 'img/p2.jpg') }}" alt="{{ $fav->story->title }}">
                    </div>
                    <div class="story-info">
                        <div class="story-title">{{ $fav->story->title }}</div>
                    </div>
                </a>
            @empty
                <p style="color:var(--text-muted);grid-column:1/-1">Belum ada cerita favorit.</p>
            @endforelse
        </div>
    </div>

    <div class="dash-card">
        <h3><i class="fa-solid fa-clock-rotate-left"></i> Riwayat Bacaan Terakhir</h3>
        <div class="history-list">
            @forelse($readingHistory as $history)
                <a href="{{ route('chapter.show', [$history->story_id, $history->chapter_id]) }}" class="history-item" style="text-decoration:none;color:inherit">
                    <img src="{{ asset($history->story->cover_image ?? 'img/p2.jpg') }}" class="history-cover" alt="">
                    <div class="history-info">
                        <h4>{{ $history->story->title ?? 'Cerita dihapus' }}</h4>
                        <p>{{ $history->chapter->title ?? 'Bab dihapus' }}</p>
                    </div>
                    <span class="history-date">{{ $history->read_at->diffForHumans() }}</span>
                </a>
            @empty
                <p style="color:var(--text-muted)">Belum ada riwayat bacaan.</p>
            @endforelse
        </div>
    </div>
</div>
@endif
@endsection
