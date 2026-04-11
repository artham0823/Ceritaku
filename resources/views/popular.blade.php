@extends('layouts.app')
@section('title', 'Cerita Populer - Ceritaku')

@section('content')
<section class="container" style="margin-top:3rem;margin-bottom:5rem;min-height:60vh">
    <div class="section-header">
        <h2><i class="fa-solid fa-fire" style="color:var(--primary-color)"></i> Cerita Populer</h2>
    </div>
    <div class="profiles-grid">
        @forelse($stories as $story)
            <a href="{{ route('story.show', $story->id) }}" class="story-card" style="animation:fadeInUp 0.5s ease forwards {{ ($loop->index % 6) * 0.1 }}s;opacity:0">
                <div class="story-cover">
                    <img src="{{ asset($story->cover_image ?? 'img/p2.jpg') }}" alt="{{ $story->title }}" loading="lazy">
                </div>
                <div class="story-info">
                    <div class="story-title" title="{{ $story->title }}">{{ $story->title }}</div>
                    <div class="story-meta">
                        <span><i class="fa-solid fa-layer-group"></i> {{ $story->chapters_count }}</span>
                        <span><i class="fa-solid fa-eye"></i> {{ number_format($story->views_count / 1000, 1) }}k</span>
                        <span><i class="fa-solid fa-heart"></i> {{ $story->likes_count }}</span>
                    </div>
                </div>
            </a>
        @empty
            <div class="empty-state" style="grid-column:1/-1">
                <i class="fa-solid fa-chart-line"></i>
                <p>Belum ada cerita populer.</p>
            </div>
        @endforelse
    </div>
</section>
@endsection
