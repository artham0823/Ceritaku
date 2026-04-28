{{-- =====================================================
     HALAMAN PROFIL PUBLIK
     =====================================================
     Menampilkan profil user yang bisa dilihat oleh
     semua pengunjung (termasuk guest).
     
     Informasi yang ditampilkan:
     - Avatar, nama, role, level, title, bio
     - Link sosial media (icon kecil berjajar horizontal)
     - Cerita karya (jika author)
     - Komentar terakhir
     ===================================================== --}}

@extends('layouts.app')
@section('title', 'Profil ' . $user->name . ' - Ceritaku')

@section('content')
<div class="container" style="max-width: 900px; padding: 4rem 2rem; min-height: 70vh;">

    {{-- Tombol Kembali --}}
    <a href="{{ url()->previous() }}" class="back-btn" style="margin-bottom: 2rem;">
        <i class="fa-solid fa-arrow-left"></i> Kembali
    </a>

    {{-- ============================================
         HEADER PROFIL
         Avatar, nama, badge role/level, bio, tanggal gabung.
         ============================================ --}}
    <div class="profile-header" style="text-align: center; margin-bottom: 3rem;">
        {{-- Foto profil --}}
        <img src="{{ asset($user->avatar ?? 'img/p2.jpg') }}" alt="Avatar" style="width: 120px; height: 120px; border-radius: 50%; 
        object-fit: cover; border: 4px solid var(--primary-color); margin-bottom: 1rem;">

        {{-- Nama user --}}
        <h1 style="font-size: 2.2rem; margin-bottom: 0.5rem;">{{ $user->name }}</h1>
        
        {{-- Badge: role, level, title --}}
        <div style="display: flex; justify-content: center; gap: 0.5rem; margin-bottom: 1rem; flex-wrap: wrap;">
            {{-- Badge role (Author/Admin/Member) --}}
            <span class="badge" style="margin: 0; background: var(--bg-card); border: 1px solid var(--primary-color);">
            {{ ucfirst($user->role) }}</span>
            {{-- Badge level (hanya untuk member) --}}
            @if($user->isMember())
                <span class="badge" style="margin: 0; background: var(--bg-card); border: 1px solid 
                var(--border-color); color: var(--text-muted);">{{ $user->getLevelName() }}</span>
            @endif
            {{-- Badge title/gelar (jika ada) --}}
            @if($user->title)
                <span class="badge" style="margin: 0; background: var(--bg-accent); border: 1px solid 
                var(--primary-light); color: var(--primary-dark);">{{ $user->title }}</span>
            @endif
        </div>

        {{-- Bio --}}
        <p style="max-width: 600px; margin: 0 auto; color: var(--text-main); line-height: 1.6;">{{ $user->bio ?? 'Belum ada bio.' }}</p>

        {{-- ============================================
             LINK SOSIAL MEDIA
             Icon kecil berjajar horizontal.
             Jika tidak muat, lanjut ke baris bawah.
             Hanya tampil jika user punya sosmed.
             ============================================ --}}
        @if($user->socialLinks->count() > 0)
            <div class="social-links-list">
                @foreach($user->socialLinks as $link)
                    @php
                        // Cek apakah value berupa URL (bisa diklik) atau hanya teks biasa
                        $isUrl = str_starts_with($link->value, 'http://') || str_starts_with($link->value, 'https://');
                    @endphp
                    @if($isUrl)
                        {{-- Link yang bisa diklik --}}
                        <a href="{{ $link->value }}" target="_blank" rel="noopener noreferrer" class="social-link-item" 
                            title="{{ $link->label }}: {{ $link->value }}">
                            <i class="{{ $link->icon }}"></i>
                            <span>{{ $link->label }}</span>
                        </a>
                    @else
                        {{-- Teks biasa (game ID, nickname, dll) --}}
                        <span class="social-link-item" title="{{ $link->label }}: {{ $link->value }}">
                            <i class="{{ $link->icon }}"></i>
                            <span>{{ $link->label }}: {{ $link->value }}</span>
                        </span>
                    @endif
                @endforeach
            </div>
        @endif

        {{-- Tanggal bergabung --}}
        <p style="color: var(--text-muted); font-size: 0.9rem; margin-top: 1.5rem;">
            <i class="fa-solid fa-calendar"></i> Bergabung sejak {{ $user->created_at->format('F Y') }}
        </p>
    </div>

    {{-- ============================================
         CERITA KARYA (Hanya tampil untuk author)
         Daftar cerita yang ditulis oleh user ini.
         ============================================ --}}
    @if($user->isAuthor() && $authoredStories->count() > 0)
    <div style="margin-bottom: 3rem;">
        <h3 style="margin-bottom: 1.5rem; border-bottom: 2px solid var(--border-color); padding-bottom: 0.5rem;"><i class="fa-solid fa-book"></i> Karya Cerita</h3>
        <div class="chapters-grid">
            @foreach($authoredStories as $story)
                <a href="{{ route('story.show', $story->id) }}" class="chapter-card" style="text-decoration:none;color:inherit;">
                    <div style="display: flex; gap: 1rem;">
                        <img src="{{ asset($story->cover_image ?? 'img/p2.jpg') }}" alt="" style="width: 70px; height: 100px; object-fit: cover; border-radius: var(--radius-sm);">
                        <div>
                            <h3 style="font-size: 1.1rem; margin-bottom: 0.5rem;">{{ $story->title }}</h3>
                            <p style="font-size: 0.85rem; margin-bottom: 0.5rem;">{{ $story->genre }} • {{ $story->chapters_count }} Bab</p>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
    @endif

    {{-- ============================================
         AKTIVITAS KOMENTAR TERAKHIR
         5 komentar terbaru dari user ini.
         ============================================ --}}
    <div>
        <h3 style="margin-bottom: 1.5rem; border-bottom: 2px solid var(--border-color); padding-bottom: 0.5rem;">
            <i class="fa-solid fa-comments"></i> Aktivitas Komentar Terakhir</h3>
        @forelse($recentComments as $comment)
            <div style="background: var(--bg-card); padding: 1.2rem; border-radius: var(--radius-sm); border: 1px solid var(--border-color); margin-bottom: 1rem;">
                {{-- Info cerita yang dikomentari + waktu --}}
                <p style="font-size: 0.9rem; color: var(--text-muted); margin-bottom: 0.5rem;">Pada cerita: 
                    <a href="{{ route('chapter.show', [$comment->chapter->story_id, $comment->chapter_id]) }}" 
                    style="color: var(--primary-color); font-weight: 600;">{{ $comment->chapter->story->title ?? '' }} 
                    ({{ $comment->chapter->title ?? '' }})</a> - {{ $comment->created_at->diffForHumans() }}</p>
                {{-- Isi komentar --}}
                <p style="color: var(--text-main); font-style: italic;">"{{ $comment->content }}"</p>
            </div>
        @empty
            <div class="empty-state" style="padding: 2rem;">
                <p>Belum ada aktivitas komentar.</p>
            </div>
        @endforelse
    </div>
</div>
@endsection
