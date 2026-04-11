@extends('layouts.app')
@section('title', $story->title . ' - Ceritaku')

@section('content')
<section class="container" style="min-height:60vh;padding-top:2rem;margin-bottom:3rem">
    <a href="{{ route('home') }}" class="back-btn"><i class="fa-solid fa-arrow-left"></i> Kembali ke Beranda</a>

    <div class="story-detail-header">
        <img src="{{ asset($story->cover_image ?? 'img/p2.jpg') }}" alt="{{ $story->title }}">
        <div class="detail-info">
            <span class="badge">{{ $story->genre }}</span>
            <h1>{{ $story->title }}</h1>
            <div class="story-meta" style="margin-bottom:1rem">
                <span><i class="fa-solid fa-layer-group"></i> {{ $story->chapters->count() }} Bab</span>
                <span><i class="fa-solid fa-eye"></i> {{ number_format($story->views_count) }} Dilihat</span>
                <span><i class="fa-solid fa-heart"></i> {{ $story->likes_count }} Like</span>
                @if($story->creator)
                    <a href="{{ route('profile.show', $story->creator->id) }}" style="text-decoration: none; color: inherit; transition: color 0.3s;" onmouseover="this.style.color='var(--primary-color)'" onmouseout="this.style.color='inherit'"><i class="fa-solid fa-user"></i> {{ $story->creator->name }}</a>
                @else
                    <span><i class="fa-solid fa-user"></i> Anonim</span>
                @endif
            </div>
            <p>{{ $story->description }}</p>

            @auth
            <div style="margin-top: 1rem; margin-bottom: 1.5rem; background: var(--bg-main); border: 1px solid var(--border-color); padding: 0.8rem; border-radius: var(--radius-sm);">
                <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem; font-size: 0.9rem; font-weight: 600;">
                    <span>Progress Membaca</span>
                    <span style="color: var(--primary-color);">Sudah dibaca {{ $readProgress }}%</span>
                </div>
                <div style="width: 100%; background: var(--bg-accent); height: 8px; border-radius: 4px; overflow: hidden;">
                    <div style="width: {{ $readProgress }}%; background: var(--primary-color); height: 100%; border-radius: 4px; border-top-right-radius: {{ $readProgress == 100 ? '4px' : '0' }}; border-bottom-right-radius: {{ $readProgress == 100 ? '4px' : '0' }}; transition: width 0.5s ease;"></div>
                </div>
            </div>
            @endauth

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
