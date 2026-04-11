@extends('layouts.app')
@section('title', 'Cerita Populer - Ceritaku')

@section('content')
<section class="container" style="margin-top:3rem;margin-bottom:5rem;min-height:60vh">
    <div class="section-header">
        <h2><i class="fa-solid fa-fire" style="color:var(--primary-color)"></i> Cerita Populer</h2>
    </div>
    @if($stories->count() > 0)
        <!-- Podium Top 3 -->
        <div class="podium-container" style="display: flex; justify-content: center; align-items: flex-end; gap: 1.5rem; margin-bottom: 4rem; margin-top: 2rem; padding: 0 1rem;">
            @if($stories->count() > 1)
                <!-- Rank 2 -->
                @php $story2 = $stories[1]; @endphp
                <a href="{{ route('story.show', $story2->id) }}" class="podium-card rank-2" style="text-decoration:none; color:inherit; width: 28%; max-width: 200px; position: relative; text-align: center; transition: transform 0.3s;">
                    <div class="medal" style="position: absolute; top: -15px; right: -15px; font-size: 2rem; z-index: 10; filter: drop-shadow(0 2px 4px rgba(0,0,0,0.2));">🥈</div>
                    <img src="{{ asset($story2->cover_image ?? 'img/p2.jpg') }}" style="width: 100%; border-radius: var(--radius-md); box-shadow: var(--shadow-md); aspect-ratio: 2/3; object-fit: cover; border: 3px solid silver;">
                    <h3 style="margin-top: 0.5rem; font-size: 1.1rem; color: var(--text-main); white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $story2->title }}</h3>
                    <span style="color: var(--text-muted); font-size: 0.9rem;"><i class="fa-solid fa-eye"></i> {{ number_format($story2->views_count) }}</span>
                </a>
            @endif

            <!-- Rank 1 -->
            @php $story1 = $stories[0]; @endphp
            <a href="{{ route('story.show', $story1->id) }}" class="podium-card rank-1" style="text-decoration:none; color:inherit; width: 35%; max-width: 250px; position: relative; text-align: center; transform: translateY(-30px); transition: transform 0.3s;">
                <div class="medal" style="position: absolute; top: -20px; right: -20px; font-size: 3rem; z-index: 10; filter: drop-shadow(0 4px 8px rgba(0,0,0,0.3));">🥇</div>
                <img src="{{ asset($story1->cover_image ?? 'img/p2.jpg') }}" style="width: 100%; border-radius: var(--radius-md); box-shadow: 0 15px 30px rgba(255, 215, 0, 0.4); aspect-ratio: 2/3; object-fit: cover; border: 4px solid gold;">
                <h3 style="margin-top: 0.8rem; font-size: 1.3rem; color: var(--primary-color); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; font-weight: 700;">{{ $story1->title }}</h3>
                <span style="color: var(--text-muted); font-size: 0.95rem; font-weight: 600;"><i class="fa-solid fa-eye"></i> {{ number_format($story1->views_count) }}</span>
            </a>

            @if($stories->count() > 2)
                <!-- Rank 3 -->
                @php $story3 = $stories[2]; @endphp
                <a href="{{ route('story.show', $story3->id) }}" class="podium-card rank-3" style="text-decoration:none; color:inherit; width: 25%; max-width: 180px; position: relative; text-align: center; transition: transform 0.3s;">
                    <div class="medal" style="position: absolute; top: -15px; right: -15px; font-size: 2rem; z-index: 10; filter: drop-shadow(0 2px 4px rgba(0,0,0,0.2));">🥉</div>
                    <img src="{{ asset($story3->cover_image ?? 'img/p2.jpg') }}" style="width: 100%; border-radius: var(--radius-md); box-shadow: var(--shadow-md); aspect-ratio: 2/3; object-fit: cover; border: 3px solid #cd7f32;">
                    <h3 style="margin-top: 0.5rem; font-size: 1rem; color: var(--text-main); white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $story3->title }}</h3>
                    <span style="color: var(--text-muted); font-size: 0.85rem;"><i class="fa-solid fa-eye"></i> {{ number_format($story3->views_count) }}</span>
                </a>
            @endif
        </div>
        
        <h3 style="margin-bottom: 1.5rem; border-bottom: 1px solid var(--border-color); padding-bottom: 0.5rem;">Peringkat Selanjutnya</h3>
    @endif

    <div class="profiles-grid">
        @forelse($stories->skip(3) as $story)
            <a href="{{ route('story.show', $story->id) }}" class="story-card" style="animation:fadeInUp 0.5s ease forwards {{ ($loop->index % 6) * 0.1 }}s;opacity:0">
                <div class="story-cover" style="position: relative;">
                    <div style="position: absolute; top: 0; left: 0; background: rgba(0,0,0,0.7); color: white; padding: 0.2rem 0.6rem; border-bottom-right-radius: var(--radius-sm); font-weight: bold; font-size: 0.85rem; z-index: 10;">
                        #{{ $loop->iteration + 3 }}
                    </div>
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
            @if($stories->count() == 0)
            <div class="empty-state" style="grid-column:1/-1">
                <i class="fa-solid fa-chart-line"></i>
                <p>Belum ada cerita populer.</p>
            </div>
            @endif
        @endforelse
    </div>
</section>
@endsection
