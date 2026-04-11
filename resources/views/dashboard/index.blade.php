@extends('layouts.dashboard')
@section('title', 'Dashboard - Ceritaku')

@section('content')
<div class="dash-header">
    <div>
        <h1>Selamat Datang, {{ $user->name }}!</h1>
        <p>{{ $user->isAuthor() ? 'Dashboard Author — Otoritas Tertinggi' : ($user->isAdmin() ? 'Dashboard Admin' : 'Dashboard Member') }}</p>
    </div>
</div>

{{-- Statistik (Author & Admin) --}}
@if($user->canManageContent())
<div class="stats-grid {{ $user->isAuthor() ? 'author-dashboard' : 'admin-dashboard' }}">
    <a href="{{ route('dashboard.stories.index') }}" class="stat-card" style="text-decoration:none; color:inherit; display:block;">
        <div class="stat-icon"><i class="fa-solid fa-book"></i></div>
        <div class="stat-value">{{ $totalStories }}</div>
        <div class="stat-label">Total Cerita</div>
    </a>
    <a href="{{ route('dashboard.stories.index') }}" class="stat-card" style="text-decoration:none; color:inherit; display:block;">
        <div class="stat-icon"><i class="fa-solid fa-file-lines"></i></div>
        <div class="stat-value">{{ $totalChapters }}</div>
        <div class="stat-label">Total Bab</div>
    </a>
    <a href="{{ route('dashboard.comments') }}" class="stat-card" style="text-decoration:none; color:inherit; display:block;">
        <div class="stat-icon"><i class="fa-solid fa-comments"></i></div>
        <div class="stat-value">{{ $totalComments }}</div>
        <div class="stat-label">Total Komentar</div>
    </a>
    <a href="{{ route('dashboard.members') }}" class="stat-card" style="text-decoration:none; color:inherit; display:block;">
        <div class="stat-icon"><i class="fa-solid fa-users"></i></div>
        <div class="stat-value">{{ $totalMembers }}</div>
        <div class="stat-label">Total Member</div>
    </a>
    @if($user->isAuthor())
    <a href="{{ route('dashboard.admins') }}" class="stat-card" style="text-decoration:none; color:inherit; display:block;">
        <div class="stat-icon"><i class="fa-solid fa-user-shield"></i></div>
        <div class="stat-value">{{ $totalAdmins }}</div>
        <div class="stat-label">Total Admin</div>
    </a>
    @endif
    <a href="{{ route('dashboard.requests') }}" class="stat-card" style="text-decoration:none; color:inherit; display:block;">
        <div class="stat-icon"><i class="fa-solid fa-inbox"></i></div>
        <div class="stat-value">{{ $pendingRequests }}</div>
        <div class="stat-label">Request Pending</div>
    </a>
</div>

{{-- Visual Statistics (Chart.js) untuk Author --}}
@if($user->isAuthor())
<div class="dash-card" style="margin-top: 1rem;">
    <h3 style="margin-bottom: 1.5rem;"><i class="fa-solid fa-chart-pie"></i> Analisis Data Ceritaku</h3>
    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 2rem; align-items: start;">
        <div style="position: relative; height: 300px; width: 100%;">
            <canvas id="readingChart"></canvas>
        </div>
        <div style="position: relative; height: 300px; width: 100%;">
            <canvas id="genreChart"></canvas>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Grafik Aktivitas Membaca (7 Hari Terakhir)
    const readingCtx = document.getElementById('readingChart').getContext('2d');
    new Chart(readingCtx, {
        type: 'line',
        data: {
            labels: {!! json_encode($readingLabels ?? []) !!},
            datasets: [{
                label: 'Jumlah Bab Dibaca',
                data: {!! json_encode($readingData ?? []) !!},
                borderColor: '#e28743',
                backgroundColor: 'rgba(226, 135, 67, 0.2)',
                borderWidth: 3,
                tension: 0.3,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                title: { display: true, text: 'Aktivitas Membaca 7 Hari Terakhir', color: '#8b7a65' }
            }
        }
    });

    // Grafik Distribusi Genre Cerita
    const genreCtx = document.getElementById('genreChart').getContext('2d');
    new Chart(genreCtx, {
        type: 'doughnut',
        data: {
            labels: {!! json_encode($genreLabels ?? []) !!},
            datasets: [{
                data: {!! json_encode($genreData ?? []) !!},
                backgroundColor: [
                    '#e28743', '#8b7a65', '#d1bfae', '#f4ecd8', '#6b5c4d'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                title: { display: true, text: 'Distribusi Genre', color: '#8b7a65' }
            }
        }
    });
});
</script>
@endif

{{-- Komentar Terbaru --}}
<div class="dash-card">
    <h3><i class="fa-solid fa-comments"></i> Komentar Terbaru</h3>
    @forelse($recentComments as $comment)
        <div class="comment-item" style="margin-bottom:0.8rem">
            <img src="{{ asset($comment->user->avatar ?? 'img/p2.jpg') }}" alt="" class="comment-avatar">
            <div class="comment-body">
                <div class="comment-header">
                    <a href="{{ route('profile.show', $comment->user->id) }}" class="comment-name" style="text-decoration:none; color:inherit;">{{ $comment->user->name }}</a>
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
        <a href="{{ route('dashboard.history') }}" class="stat-card" style="text-decoration:none; color:inherit; display:block;">
            <div class="stat-icon"><i class="fa-solid fa-clock-rotate-left"></i></div>
            <div class="stat-value">{{ $totalStoriesRead }}</div>
            <div class="stat-label">Cerita Mengikuti / Dibaca</div>
        </a>
        <a href="#favorites" class="stat-card" style="text-decoration:none; color:inherit; display:block;">
            <div class="stat-icon"><i class="fa-solid fa-bookmark"></i></div>
            <div class="stat-value">{{ $favorites->count() }}</div>
            <div class="stat-label">Cerita Favorit</div>
        </a>
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

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; margin-top: 2rem;">
        <div class="dash-card">
            <h3><i class="fa-solid fa-comment-dots"></i> Komentar Terakhir Saya</h3>
            @forelse($userComments as $comment)
                <div style="padding: 1rem 0; border-bottom: 1px solid var(--border-color);">
                    <small style="color:var(--primary-color); font-weight: 600;">{{ $comment->chapter->story->title ?? '' }}</small>
                    <p style="margin: 0.3rem 0; font-size: 0.95rem;">"{{ $comment->content }}"</p>
                    <small style="color:var(--text-muted)">{{ $comment->created_at->diffForHumans() }}</small>
                </div>
            @empty
                <p style="color:var(--text-muted); padding-top: 1rem;">Anda belum pernah berkomentar.</p>
            @endforelse
        </div>

        <div class="dash-card">
            <h3><i class="fa-solid fa-lightbulb"></i> Status Request Cerita</h3>
            @forelse($userRequests as $req)
                <div style="padding: 1rem 0; border-bottom: 1px solid var(--border-color);">
                    <div style="display: flex; justify-content: space-between; align-items: start;">
                        <span style="font-weight: 600;">{{ $req->title }}</span>
                        @if($req->status === 'pending')
                            <span class="badge" style="background: rgba(214,158,46,0.1); color: var(--warning); margin: 0; font-size: 0.75rem;">Pending</span>
                        @elseif($req->status === 'approved')
                            <span class="badge" style="background: rgba(56,161,105,0.1); color: var(--success); margin: 0; font-size: 0.75rem;">Disetujui</span>
                        @else
                            <span class="badge" style="background: rgba(229,62,62,0.1); color: var(--danger); margin: 0; font-size: 0.75rem;">Ditolak</span>
                        @endif
                    </div>
                    <small style="color:var(--text-muted); display: block; margin-top: 0.5rem;">Dikirim {{ $req->created_at->diffForHumans() }}</small>
                </div>
            @empty
                <p style="color:var(--text-muted); padding-top: 1rem;">Belum ada request cerita.</p>
            @endforelse
        </div>
    </div>
</div>
@endif
@endsection
