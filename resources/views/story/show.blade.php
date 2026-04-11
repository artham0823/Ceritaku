@extends('layouts.app')
@section('title', $story->title . ' - Ceritaku')

@section('content')
<section class="container" style="min-height:60vh;padding-top:2rem;margin-bottom:3rem">
    <a href="{{ url()->previous() }}" class="back-btn"><i class="fa-solid fa-arrow-left"></i> Kembali</a>

    <div class="story-detail-header">
        <img src="{{ asset($story->cover_image ?? 'img/p2.jpg') }}" alt="{{ $story->title }}">
        <div class="detail-info">
            <span class="badge">{{ $story->genre }}</span>
            <h1>{{ $story->title }}</h1>
            <div class="story-meta" style="margin-bottom:1rem">
                <span><i class="fa-solid fa-layer-group"></i> {{ $story->chapters->count() }} Bab</span>
                <span><i class="fa-solid fa-eye"></i> {{ number_format($story->views_count) }} Dilihat</span>
                <span><i class="fa-solid fa-heart"></i> {{ $story->likes_count }} Like</span>
                <span><i class="fa-solid fa-user"></i> {{ $story->creator->name ?? 'Anonim' }}</span>
            </div>
            <p>{{ $story->description }}</p>

            <div class="story-actions">
                @if($story->chapters->count() > 0)
                    <a href="{{ route('chapter.show', [$story->id, $story->chapters->first()->id]) }}" class="btn btn-primary">
                        <i class="fa-solid fa-book-open"></i> Mulai Membaca
                    </a>
                @endif

                <form action="{{ route('like.toggle', $story->id) }}" method="POST" style="display:inline">
                    @csrf
                    <button type="submit" class="btn-like {{ $isLiked ? 'active' : '' }}">
                        <i class="fa-{{ $isLiked ? 'solid' : 'regular' }} fa-heart"></i>
                        {{ $isLiked ? 'Liked' : 'Like' }}
                    </button>
                </form>

                @auth
                    <form action="{{ route('favorite.toggle', $story->id) }}" method="POST" style="display:inline">
                        @csrf
                        <button type="submit" class="btn-favorite {{ $isFavorited ? 'active' : '' }}">
                            <i class="fa-{{ $isFavorited ? 'solid' : 'regular' }} fa-bookmark"></i>
                            {{ $isFavorited ? 'Favorit' : 'Favoritkan' }}
                        </button>
                    </form>
                @endauth
            </div>
        </div>
    </div>

    <div style="margin-top:2rem">
        <h3 style="font-size:1.3rem;margin-bottom:1.5rem">Daftar Bab</h3>
        <div class="chapters-grid">
            @forelse($story->chapters as $chapter)
                <a href="{{ route('chapter.show', [$story->id, $chapter->id]) }}" class="chapter-card" style="text-decoration:none;color:inherit;animation:fadeInUp 0.5s ease forwards {{ $loop->index * 0.05 }}s;opacity:0">
                    <h3>{{ $chapter->title }}</h3>
                    <p>{{ $chapter->preview }}</p>
                    <span class="chapter-read-btn">
                        <i class="fa-solid fa-book-open"></i> Baca
                    </span>
                </a>
            @empty
                <div class="empty-state" style="grid-column:1/-1">
                    <p>Belum ada bab yang tersedia.</p>
                </div>
            @endforelse
        </div>
    </div>
</section>
@endsection
