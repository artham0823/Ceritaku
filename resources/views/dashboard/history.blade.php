@extends('layouts.dashboard')
@section('title', 'Riwayat Bacaan - Ceritaku')

@section('content')
<div class="dash-header">
    <h1>Riwayat Bacaan</h1>
</div>

<div class="dash-card">
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
            <div class="empty-state">
                <i class="fa-solid fa-clock-rotate-left"></i>
                <p>Belum ada riwayat bacaan.</p>
            </div>
        @endforelse
    </div>
    <div class="pagination">{{ $readingHistory->links() }}</div>
</div>
@endsection
