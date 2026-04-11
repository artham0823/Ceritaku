@extends('layouts.app')
@section('title', 'Cari Cerita - Ceritaku')

@section('content')
<section class="container" style="margin-top:3rem;margin-bottom:5rem;min-height:60vh">
    <div class="section-header">
        <h2>Hasil Pencarian</h2>
    </div>
    @if($keyword)
        <p style="color:var(--text-muted);margin-bottom:2rem">Menampilkan hasil untuk: <strong>"{{ $keyword }}"</strong> ({{ $stories->count() }} cerita)</p>
    @endif
    <div class="profiles-grid">
        @forelse($stories as $story)
            <a href="{{ route('story.show', $story->id) }}" class="story-card">
                <div class="story-cover">
                    <img src="{{ asset($story->cover_image ?? 'img/p2.jpg') }}" alt="{{ $story->title }}" loading="lazy">
                </div>
                <div class="story-info">
                    <div class="story-title">{{ $story->title }}</div>
                    <div class="story-meta">
                        <span><i class="fa-solid fa-layer-group"></i> {{ $story->chapters_count }}</span>
                        <span><i class="fa-solid fa-eye"></i> {{ number_format($story->views_count / 1000, 1) }}k</span>
                    </div>
                </div>
            </a>
        @empty
            <div class="empty-state" style="grid-column:1/-1">
                <i class="fa-solid fa-search"></i>
                <p>{{ $keyword ? 'Tidak ada cerita ditemukan untuk "' . $keyword . '".' : 'Masukkan kata kunci untuk mencari cerita.' }}</p>
            </div>
        @endforelse
    </div>
</section>
@endsection
