{{-- =====================================================
     HALAMAN BACA CHAPTER (Reader)
     =====================================================
     Halaman immersive untuk membaca cerita.
     
     Fitur:
     - Pengaturan tema (Terang/Gelap/Sepia/Neon)
     - Pengaturan font (Modern/Klasik)
     - Pengaturan ukuran teks (Kecil/Sedang/Besar)
     - Navigasi chapter (prev/next)
     - Watermark username (anti-screenshot)
     - Reaksi emoji & komentar
     - Mode fullscreen (klik area baca)
     ===================================================== --}}

@extends('layouts.app')
@section('title', $chapter->title . ' - ' . $story->title)
@push('styles')
<link rel="stylesheet" href="{{ asset('css/reader.css') }}">
@endpush

{{-- Sembunyikan section request cerita di halaman baca --}}
@php $hideRequest = true; @endphp

@section('content')
<div class="reader-page">
    {{-- ============================================
         READER NAVBAR
         Navigasi atas saat membaca: kembali, judul,
         progress, dan tombol pengaturan baca.
         ============================================ --}}
    <header class="reader-navbar" style="position: relative;">
        {{-- Tombol kembali ke halaman detail cerita --}}
        <a href="{{ route('story.show', $story->id) }}" class="reader-back-btn">
            <i class="fa-solid fa-arrow-left"></i>
            <span>Kembali</span>
        </a>

        {{-- Judul chapter mini di tengah --}}
        <div class="reader-title-mini">{{ $chapter->title }}</div>
        
        {{-- Area kanan: progress + tombol pengaturan --}}
        <div style="display:flex; gap:0.8rem; align-items:center; flex-shrink:0;">
            {{-- Label progress chapter --}}
            <span class="reader-progress">{{ $chapter->chapter_number }} / {{ $story->chapters->count() }}</span>

            {{-- Tombol buka pengaturan baca --}}
            <button id="reader-settings-btn" style="background:none; border:none; color:var(--text-main); font-size:1.2rem; cursor:pointer; padding:0.3rem;" aria-label="Pengaturan Baca">
                <i class="fa-solid fa-font"></i>
            </button>

            {{-- ============================================
                 DROPDOWN PENGATURAN BACA
                 Tema background, gaya font, ukuran teks.
                 ============================================ --}}
            <div class="reader-settings-dropdown" id="reader-settings-dropdown">
                {{-- Pilihan tema --}}
                <div class="setting-group">
                    <label>Tema Background</label>
                    <div class="setting-options">
                        <button class="setting-btn" data-setting="theme" data-value="light">Terang</button>
                        <button class="setting-btn" data-setting="theme" data-value="sepia">Sepia</button>
                        <button class="setting-btn" data-setting="theme" data-value="dark">Gelap</button>
                        <button class="setting-btn" data-setting="theme" data-value="neon">Neon</button>
                    </div>
                </div>
                {{-- Pilihan gaya font --}}
                <div class="setting-group">
                    <label>Gaya Font</label>
                    <div class="setting-options">
                        <button class="setting-btn" data-setting="font" data-value="sans">Modern</button>
                        <button class="setting-btn" data-setting="font" data-value="serif">Klasik</button>
                    </div>
                </div>
                {{-- Pilihan ukuran teks --}}
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

    {{-- ============================================
         WATERMARK USERNAME
         Overlay transparan berisi username user yang login.
         Untuk mencegah screenshot & share tanpa izin.
         ============================================ --}}
    @auth
    <div class="dynamic-watermark" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: 5; pointer-events: none; overflow: hidden; opacity: 0.04; display: flex; flex-wrap: wrap; justify-content: center; align-items: center; color: var(--text-main); font-size: 2.2rem; font-weight: bold; transform: rotate(-30deg) scale(1.5);">
        @for($i=0; $i<60; $i++)
            <span style="margin: 3.5rem;">{{ auth()->user()->username }}</span>
        @endfor
    </div>
    @endauth

    {{-- ============================================
         KONTEN CERITA (Anti-copy)
         Area utama teks cerita. Di-protect dari
         salin dan klik kanan.
         ============================================ --}}
    <div class="reader-content no-copy">
        <div class="reader-chapter-title">{{ $chapter->title }}</div>
        <div class="reader-chapter-body">
            {!! $chapter->content !!}
        </div>
    </div>

    {{-- ============================================
         NAVIGASI CHAPTER (Prev / Next)
         Tombol untuk pindah ke chapter sebelumnya
         atau selanjutnya.
         ============================================ --}}
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

    {{-- ============================================
         REAKSI EMOJI
         User bisa memberikan reaksi pada chapter
         (Suka, Cinta, Lucu, Kaget, Sedih).
         Hanya user yang login yang bisa bereaksi.
         ============================================ --}}
    <div class="container" style="max-width:750px; margin: 3rem auto 1rem; text-align: center; position: relative; z-index: 10;">
        <h4 style="margin-bottom: 1rem; color: var(--text-muted); font-weight: 500;">Bagaimana perasaan Anda tentang bab ini?</h4>
        <div class="reactions-container" style="display: flex; justify-content: center; gap: 1rem; flex-wrap: wrap;">
            @php
                // Ambil reaksi user saat ini (jika sudah login)
                $userReaction = auth()->check() ? \App\Models\ChapterReaction::where('chapter_id', $chapter->id)->where('user_id', auth()->id())->value('reaction_type') : null;
                
                // Daftar reaksi yang tersedia
                $reactions = [
                    'like' => ['icon' => '👍', 'label' => 'Suka'],
                    'love' => ['icon' => '❤️', 'label' => 'Cinta'],
                    'laugh' => ['icon' => '😂', 'label' => 'Lucu'],
                    'wow' => ['icon' => '😱', 'label' => 'Kaget'],
                    'cry' => ['icon' => '😭', 'label' => 'Sedih']
                ];

                // Hitung jumlah reaksi per tipe
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

    {{-- ============================================
         KOMENTAR SECTION
         User bisa menulis dan melihat komentar.
         Limit komentar per hari tergantung level user.
         ============================================ --}}
    <div class="container" style="max-width:750px;margin:2rem auto;padding-bottom:3rem">
        <div class="comment-section">
            <h3><i class="fa-solid fa-comments"></i> Komentar ({{ $comments->count() }})</h3>

            @auth
                {{-- Cek apakah user masih bisa berkomentar hari ini --}}
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
                    {{-- Limit komentar tercapai --}}
                    <div class="login-prompt">
                        <p>Anda sudah mencapai batas komentar hari ini. Coba lagi besok!</p>
                    </div>
                @endif
            @else
                {{-- Belum login --}}
                <div class="login-prompt">
                    <p><a href="{{ route('login') }}">Login</a> atau <a href="{{ route('register') }}">buat akun</a> untuk berkomentar.</p>
                </div>
            @endauth

            {{-- Daftar komentar --}}
            @foreach($comments as $comment)
                <div class="comment-item">
                    {{-- Avatar komentar (klik untuk ke profil) --}}
                    <a href="{{ route('profile.show', $comment->user->id) }}">
                        <img src="{{ asset($comment->user->avatar ?? 'img/p2.jpg') }}" alt="" class="comment-avatar">
                    </a>
                    <div class="comment-body">
                        <div class="comment-header">
                            <div>
                                {{-- Nama user + badge role + badge level --}}
                                <a href="{{ route('profile.show', $comment->user->id) }}" class="comment-name" style="text-decoration: none; color: inherit; transition: color 0.3s; display: inline-block;">{{ $comment->user->name }}</a>
                                <span class="comment-role {{ $comment->user->role }}">{{ ucfirst($comment->user->role) }}</span>
                                @if($comment->user->isMember())
                                    <span style="font-size: 0.7rem; background: var(--bg-accent); padding: 0.1rem 0.4rem; border-radius: 4px; margin-left: 0.3rem; color: var(--text-muted);">{{ $comment->user->getLevelName() }}</span>
                                @endif
                            </div>
                            <span class="comment-date">{{ $comment->created_at->diffForHumans() }}</span>
                        </div>
                        <p class="comment-text">{{ $comment->content }}</p>
                        {{-- Tombol hapus komentar (hanya tampil jika punya hak) --}}
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

            {{-- Pesan jika belum ada komentar --}}
            @if($comments->isEmpty())
                <div class="empty-state">
                    <p>Belum ada komentar. Jadilah yang pertama!</p>
                </div>
            @endif
        </div>
    </div>
</div>

{{-- ============================================
     JAVASCRIPT READER
     Mengelola: pengaturan baca, sinkronisasi tema
     dengan tema global, mode fullscreen.
     ============================================ --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const readerPage = document.querySelector('.reader-page');
        const settingsBtn = document.getElementById('reader-settings-btn');
        const settingsDropdown = document.getElementById('reader-settings-dropdown');
        const settingBtns = document.querySelectorAll('.setting-btn');

        // ============================================
        // TOGGLE DROPDOWN PENGATURAN
        // Buka/tutup panel pengaturan saat tombol diklik.
        // ============================================
        settingsBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            settingsDropdown.classList.toggle('show');
        });

        // Tutup dropdown saat klik di luar
        document.addEventListener('click', (e) => {
            if (!settingsDropdown.contains(e.target) && e.target !== settingsBtn) {
                settingsDropdown.classList.remove('show');
            }
        });

        // ============================================
        // MUAT PENGATURAN DARI LOCAL STORAGE
        // Ambil pengaturan yang tersimpan, atau gunakan
        // default berdasarkan tema global saat ini.
        // ============================================
        const globalTheme = localStorage.getItem('ceritaku-theme');
        const defaults = {
            theme: globalTheme === 'dark' ? 'dark' : 'light',
            font: 'sans',
            size: 'md'
        };

        let savedSettings = JSON.parse(localStorage.getItem('readerSettings'));
        if (!savedSettings) {
            savedSettings = defaults;
        }

        // ============================================
        // MODE FULLSCREEN (Sembunyikan Navbar)
        // Klik area baca untuk sembunyikan/tampilkan
        // navbar. Tidak berlaku jika mengklik tombol,
        // link, form, komentar, dll.
        // ============================================
        const mainNavbar = document.getElementById('main-navbar');
        const readerNavbarLocal = document.querySelector('.reader-navbar');
        
        document.addEventListener('click', (e) => {
            // Jangan toggle jika klik di navbar
            if (mainNavbar && mainNavbar.contains(e.target)) return;
            if (readerNavbarLocal && readerNavbarLocal.contains(e.target)) return;
            
            // Jangan toggle jika klik elemen interaktif
            if (e.target.closest('button, a, input, textarea, form, .comment-section, .reactions-container, .reader-settings-dropdown')) return;

            // Toggle sembunyikan/tampilkan navbar
            const isHidden = document.body.classList.toggle('hide-navs');
            if (isHidden) {
                if (mainNavbar) mainNavbar.style.display = 'none';
                if (readerNavbarLocal) readerNavbarLocal.style.display = 'none';
            } else {
                if (mainNavbar) mainNavbar.style.display = '';
                if (readerNavbarLocal) readerNavbarLocal.style.display = '';
            }
        });

        // ============================================
        // TERAPKAN PENGATURAN BACA
        // Fungsi ini menerapkan tema, font, dan ukuran
        // ke halaman reader, serta menyinkronkan tema
        // global (dark/light) termasuk icon-nya.
        // ============================================
        function applySettings(settings) {
            // --- Terapkan Tema ---
            // Hapus semua class tema dari reader
            readerPage.classList.remove('theme-sepia', 'theme-neon');
            document.body.classList.remove('dark-theme');

            if (settings.theme === 'dark') {
                // Gelap: aktifkan dark-theme di body (tema global)
                document.body.classList.add('dark-theme');
                document.body.classList.remove('light-theme');
                // Sinkronkan tema global
                if (typeof window.setTheme === 'function') {
                    window.setTheme('dark');
                } else {
                    localStorage.setItem('ceritaku-theme', 'dark');
                }
            } else if (settings.theme === 'sepia') {
                // Sepia: hanya reader yang berubah, body tetap light
                readerPage.classList.add('theme-sepia');
                document.body.classList.add('light-theme');
                localStorage.setItem('ceritaku-theme', 'light');
                if (typeof window.updateThemeIcons === 'function') {
                    window.updateThemeIcons(false);
                }
            } else if (settings.theme === 'neon') {
                // Neon: reader berubah neon + body jadi dark
                readerPage.classList.add('theme-neon');
                document.body.classList.add('dark-theme');
                document.body.classList.remove('light-theme');
                // Simpan sebagai dark di global
                localStorage.setItem('ceritaku-theme', 'dark');
                if (typeof window.updateThemeIcons === 'function') {
                    window.updateThemeIcons(true);
                }
            } else {
                // Terang: tema light biasa
                document.body.classList.add('light-theme');
                if (typeof window.setTheme === 'function') {
                    window.setTheme('light');
                } else {
                    localStorage.setItem('ceritaku-theme', 'light');
                }
            }

            // --- Terapkan Font ---
            readerPage.classList.remove('font-sans', 'font-serif');
            readerPage.classList.add('font-' + settings.font);

            // --- Terapkan Ukuran Teks ---
            readerPage.classList.remove('size-sm', 'size-md', 'size-lg');
            readerPage.classList.add('size-' + settings.size);

            // --- Update Tombol Aktif di Dropdown ---
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

        // Terapkan pengaturan yang tersimpan
        applySettings(savedSettings);

        // ============================================
        // HANDLE PERUBAHAN PENGATURAN
        // Saat user klik salah satu tombol pengaturan,
        // simpan pilihan dan terapkan langsung.
        // ============================================
        settingBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                const setting = this.getAttribute('data-setting');
                const val = this.getAttribute('data-value');
                savedSettings[setting] = val;
                // Simpan ke localStorage
                localStorage.setItem('readerSettings', JSON.stringify(savedSettings));
                // Terapkan perubahan
                applySettings(savedSettings);
            });
        });
    });
</script>
@endsection
