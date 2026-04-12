@extends('layouts.app')
@section('title', $chapter->title . ' - ' . $story->title)
@push('styles')
<link rel="stylesheet" href="{{ asset('css/reader.css') }}">
@endpush

@php $hideRequest = true; @endphp

@section('content')
<div class="reader-page">
    {{-- Reader Navbar --}}
    <header class="reader-navbar" style="position: relative;">
        <a href="{{ route('story.show', $story->id) }}" class="reader-back-btn">
            <i class="fa-solid fa-arrow-left"></i>
            <span>Kembali</span>
        </a>
        <div class="reader-title-mini">{{ $chapter->title }}</div>
        
        <div style="display:flex; gap:1rem; align-items:center;">
            <span class="reader-progress">{{ $chapter->chapter_number }} / {{ $story->chapters->count() }}</span>
            <button id="reader-settings-btn" style="background:none; border:none; color:var(--text-main); font-size:1.2rem; cursor:pointer;" aria-label="Pengaturan Baca"><i class="fa-solid fa-font"></i></button>
            <div class="reader-settings-dropdown" id="reader-settings-dropdown">
                <div class="setting-group">
                    <label>Tema Background</label>
                    <div class="setting-options">
                        <button class="setting-btn" data-setting="theme" data-value="light">Terang</button>
                        <button class="setting-btn" data-setting="theme" data-value="sepia">Sepia</button>
                        <button class="setting-btn" data-setting="theme" data-value="dark">Gelap</button>
                    </div>
                </div>
                <div class="setting-group">
                    <label>Gaya Font</label>
                    <div class="setting-options">
                        <button class="setting-btn" data-setting="font" data-value="sans">Modern</button>
                        <button class="setting-btn" data-setting="font" data-value="serif">Klasik</button>
                    </div>
                </div>
                <div class="setting-group">
                    <label>Ukuran Teks</label>
                    <div class="setting-options">
                        <button class="setting-btn" data-setting="size" data-value="sm">Kecil</button>
                        <button class="setting-btn" data-setting="size" data-value="md">Sedang</button>
                        <button class="setting-btn" data-setting="size" data-value="lg">Besar</button>
                    </div>
                </div>
            </div>
        </div>
    </header>

    @auth
    <div class="dynamic-watermark" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: 5; pointer-events: none; overflow: hidden; opacity: 0.04; display: flex; flex-wrap: wrap; justify-content: center; align-items: center; color: var(--text-main); font-size: 2.2rem; font-weight: bold; transform: rotate(-30deg) scale(1.5);">
        @for($i=0; $i<60; $i++)
            <span style="margin: 3.5rem;">{{ auth()->user()->username }}</span>
        @endfor
    </div>
    @endauth

    {{-- Reader Content (Anti-copy) --}}
    <div class="reader-content no-copy">
        <div class="reader-chapter-title">{{ $chapter->title }}</div>
        <div class="reader-chapter-body">
            {!! $chapter->content !!}
        </div>
    </div>

    {{-- Navigation --}}
    <div class="reader-nav">
        @if($prevChapter)
            <a href="{{ route('chapter.show', [$story->id, $prevChapter->id]) }}" class="nav-prev">
                <i class="fa-solid fa-chevron-left"></i> Sebelumnya
            </a>
        @else
            <span class="nav-disabled"><i class="fa-solid fa-chevron-left"></i> Sebelumnya</span>
        @endif

        <span class="reader-progress">Bab {{ $chapter->chapter_number }} dari {{ $story->chapters->count() }}</span>

        @if($nextChapter)
            <a href="{{ route('chapter.show', [$story->id, $nextChapter->id]) }}" class="nav-next">
                Selanjutnya <i class="fa-solid fa-chevron-right"></i>
            </a>
        @else
            <span class="nav-disabled">Selanjutnya <i class="fa-solid fa-chevron-right"></i></span>
        @endif
    </div>

    {{-- Reaction Section --}}
    <div class="container" style="max-width:750px; margin: 3rem auto 1rem; text-align: center; position: relative; z-index: 10;">
        <h4 style="margin-bottom: 1rem; color: var(--text-muted); font-weight: 500;">Bagaimana perasaan Anda tentang bab ini?</h4>
        <div class="reactions-container" style="display: flex; justify-content: center; gap: 1rem; flex-wrap: wrap;">
            @php
                $userReaction = auth()->check() ? \App\Models\ChapterReaction::where('chapter_id', $chapter->id)->where('user_id', auth()->id())->value('reaction_type') : null;
                
                $reactions = [
                    'like' => ['icon' => '👍', 'label' => 'Suka'],
                    'love' => ['icon' => '❤️', 'label' => 'Cinta'],
                    'laugh' => ['icon' => '😂', 'label' => 'Lucu'],
                    'wow' => ['icon' => '😱', 'label' => 'Kaget'],
                    'cry' => ['icon' => '😭', 'label' => 'Sedih']
                ];

                $reactionCounts = \App\Models\ChapterReaction::selectRaw('reaction_type, count(*) as count')
                    ->where('chapter_id', $chapter->id)
                    ->groupBy('reaction_type')
                    ->pluck('count', 'reaction_type')
                    ->toArray();
            @endphp
            
            @foreach($reactions as $key => $reaction)
                <form action="{{ route('chapter.react', $chapter->id) }}" method="POST" style="display:inline-block;">
                    @csrf
                    <input type="hidden" name="reaction_type" value="{{ $key }}">
                    <button type="submit" class="reaction-btn" {{ !auth()->check() ? 'disabled' : '' }} title="{{ !auth()->check() ? 'Login untuk bereaksi' : $reaction['label'] }}" style="background: var(--bg-card); border: 1px solid {{ $userReaction === $key ? 'var(--primary-color)' : 'var(--border-color)' }}; border-radius: var(--radius-md); padding: 0.8rem 1.2rem; cursor: {{ auth()->check() ? 'pointer' : 'not-allowed' }}; transition: var(--transition); transform: scale({{ $userReaction === $key ? '1.1' : '1' }}); box-shadow: {{ $userReaction === $key ? '0 4px 10px rgba(0,0,0,0.1)' : 'none' }}; opacity: {{ !auth()->check() ? '0.6' : '1' }};">
                        <span style="font-size: 1.8rem; display: block; margin-bottom: 0.3rem;">{{ $reaction['icon'] }}</span>
                        <span style="font-size: 0.85rem; font-weight: 700; color: {{ $userReaction === $key ? 'var(--primary-color)' : 'var(--text-muted)' }};">{{ $reactionCounts[$key] ?? 0 }}</span>
                    </button>
                </form>
            @endforeach
        </div>
    </div>

    {{-- Comment Section --}}
    <div class="container" style="max-width:750px;margin:2rem auto;padding-bottom:3rem">
        <div class="comment-section">
            <h3><i class="fa-solid fa-comments"></i> Komentar ({{ $comments->count() }})</h3>

            @auth
                @if(auth()->user()->canComment())
                    <div class="comment-form">
                        <form action="{{ route('comment.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="chapter_id" value="{{ $chapter->id }}">
                            <textarea name="content" placeholder="Tulis komentar..." required>{{ old('content') }}</textarea>
                            <div class="comment-actions">
                                <span class="comment-remaining">Sisa komentar hari ini: {{ auth()->user()->remainingComments() }}</span>
                                <button type="submit" class="btn btn-primary" style="padding:0.5rem 1.2rem;font-size:0.9rem">
                                    <i class="fa-solid fa-paper-plane"></i> Kirim
                                </button>
                            </div>
                        </form>
                    </div>
                @else
                    <div class="login-prompt">
                        <p>Anda sudah mencapai batas komentar hari ini. Coba lagi besok!</p>
                    </div>
                @endif
            @else
                <div class="login-prompt">
                    <p><a href="{{ route('login') }}">Login</a> atau <a href="{{ route('register') }}">buat akun</a> untuk berkomentar.</p>
                </div>
            @endauth

            @foreach($comments as $comment)
                <div class="comment-item">
                    <a href="{{ route('profile.show', $comment->user->id) }}">
                        <img src="{{ asset($comment->user->avatar ?? 'img/p2.jpg') }}" alt="" class="comment-avatar">
                    </a>
                    <div class="comment-body">
                        <div class="comment-header">
                            <div>
                                <a href="{{ route('profile.show', $comment->user->id) }}" class="comment-name" style="text-decoration: none; color: inherit; transition: color 0.3s; display: inline-block;">{{ $comment->user->name }}</a>
                                <span class="comment-role {{ $comment->user->role }}">{{ ucfirst($comment->user->role) }}</span>
                                @if($comment->user->isMember())
                                    <span style="font-size: 0.7rem; background: var(--bg-accent); padding: 0.1rem 0.4rem; border-radius: 4px; margin-left: 0.3rem; color: var(--text-muted);">{{ $comment->user->getLevelName() }}</span>
                                @endif
                            </div>
                            <span class="comment-date">{{ $comment->created_at->diffForHumans() }}</span>
                        </div>
                        <p class="comment-text">{{ $comment->content }}</p>
                        @auth
                            @if(
                                auth()->user()->isAuthor() ||
                                (auth()->user()->isAdmin() && !$comment->user->isAuthor()) ||
                                auth()->id() === $comment->user_id
                            )
                                <form action="{{ route('comment.destroy', $comment->id) }}" method="POST" style="margin-top:0.3rem" onsubmit="return confirm('Hapus komentar ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="comment-delete"><i class="fa-solid fa-trash"></i> Hapus</button>
                                </form>
                            @endif
                        @endauth
                    </div>
                </div>
            @endforeach

            @if($comments->isEmpty())
                <div class="empty-state">
                    <p>Belum ada komentar. Jadilah yang pertama!</p>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const readerPage = document.querySelector('.reader-page');
        const settingsBtn = document.getElementById('reader-settings-btn');
        const settingsDropdown = document.getElementById('reader-settings-dropdown');
        const settingBtns = document.querySelectorAll('.setting-btn');

        // Toggle dropdown
        settingsBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            settingsDropdown.classList.toggle('show');
        });

        // Click outside to close
        document.addEventListener('click', (e) => {
            if (!settingsDropdown.contains(e.target) && e.target !== settingsBtn) {
                settingsDropdown.classList.remove('show');
            }
        });

        // Load settings from localStorage
        const globalTheme = localStorage.getItem('ceritaku-theme');
        const defaults = { theme: globalTheme === 'dark' ? 'dark' : 'light', font: 'sans', size: 'md' };
        let savedSettings = JSON.parse(localStorage.getItem('readerSettings'));
        if (!savedSettings) {
            savedSettings = defaults;
        } else {
            // Sinkronisasi tema global dengan tema reader jika bertabrakan
            if (globalTheme === 'dark' && savedSettings.theme !== 'dark') savedSettings.theme = 'dark';
            else if (globalTheme === 'light' && savedSettings.theme === 'dark') savedSettings.theme = 'light';
        }

        // Fullscreen reading mode (hide navbars on click)
        const mainNavbar = document.getElementById('main-navbar');
        const readerNavbarLocal = document.querySelector('.reader-navbar');
        
        document.addEventListener('click', (e) => {
            if (mainNavbar && mainNavbar.contains(e.target)) return;
            if (readerNavbarLocal && readerNavbarLocal.contains(e.target)) return;
            
            // Jangan hide jika mengklik tombol, link, form, area komentar, setting, atau interaksi lainnya
            if (e.target.closest('button, a, input, textarea, form, .comment-section, .reactions-container, .reader-settings-dropdown')) return;

            // Toggle sembunyikan navbar
            const isHidden = document.body.classList.toggle('hide-navs');
            if (isHidden) {
                if (mainNavbar) mainNavbar.style.display = 'none';
                if (readerNavbarLocal) readerNavbarLocal.style.display = 'none';
            } else {
                if (mainNavbar) mainNavbar.style.display = '';
                if (readerNavbarLocal) readerNavbarLocal.style.display = '';
            }
        });

        // Note: For 'dark' theme, we use body dark-theme. For 'sepia', we apply to reader-page.
        function applySettings(settings) {
            // Apply Theme
            document.body.classList.remove('dark-theme');
            readerPage.classList.remove('theme-sepia');
            if (settings.theme === 'dark') document.body.classList.add('dark-theme');
            else if (settings.theme === 'sepia') readerPage.classList.add('theme-sepia');

            // Apply Font
            readerPage.classList.remove('font-sans', 'font-serif');
            readerPage.classList.add('font-' + settings.font);

            // Apply Size
            readerPage.classList.remove('size-sm', 'size-md', 'size-lg');
            readerPage.classList.add('size-' + settings.size);

            // Update Active Buttons
            settingBtns.forEach(btn => {
                const setting = btn.getAttribute('data-setting');
                const val = btn.getAttribute('data-value');
                if (settings[setting] === val) {
                    btn.classList.add('active');
                } else {
                    btn.classList.remove('active');
                }
            });
        }

        applySettings(savedSettings);

        // Handle Setting Changes
        settingBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                const setting = this.getAttribute('data-setting');
                const val = this.getAttribute('data-value');
                savedSettings[setting] = val;
                localStorage.setItem('readerSettings', JSON.stringify(savedSettings));
                applySettings(savedSettings);
            });
        });
    });
</script>
@endsection
