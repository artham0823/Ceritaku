@extends('layouts.dashboard')
@section('title', 'Riwayat Bacaan - Ceritaku')

@section('content')
<div class="dash-header">
    <h1>Riwayat Bacaan</h1>
</div>

<div class="dash-card">
    <div class="history-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
        @forelse($readingHistory as $history)
            <a href="{{ route('chapter.show', [$history->story_id, $history->chapter_id]) }}" class="history-card" style="text-decoration:none; color:inherit; border: 1px solid var(--border-color); border-radius: var(--radius-sm); overflow: hidden; transition: box-shadow 0.3s ease; display: block;">
                <img src="{{ asset($history->story->cover_image ?? 'img/p2.jpg') }}" class="history-cover" alt="" style="width: 100%; height: 250px; object-fit: cover;">
                <div class="history-info" style="padding: 1rem;">
                    <h4 style="font-weight: 600; font-size: 1.05rem; margin-bottom: 0.3rem;">{{ $history->story->title ?? 'Cerita dihapus' }}</h4>
                    <p style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 0.5rem;">{{ $history->chapter->title ?? 'Bab dihapus' }}</p>
                    <span style="font-size: 0.75rem; color: var(--primary-color); font-weight: 600;">Terakhir dibaca: {{ \Carbon\Carbon::parse($history->read_at)->diffForHumans() }}</span>
                </div>
            </a>
        @empty
            <div class="empty-state" style="grid-column: 1/-1;">
                <i class="fa-solid fa-clock-rotate-left"></i>
                <p>Belum ada riwayat bacaan.</p>
            </div>
        @endforelse
    </div>
    <div class="pagination">{{ $readingHistory->links() }}</div>
</div>
@endsection
