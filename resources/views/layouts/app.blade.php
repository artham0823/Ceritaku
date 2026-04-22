<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Ceritaku - Platform membaca dan membagi kisah yang terinspirasi dari hidup.">
    <title>@yield('title', 'Ceritaku - Platform Cerita Digital')</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    @stack('styles')
</head>
{{-- Body: class awal "light-theme", bisa berubah via JS --}}
<body class="light-theme">

    {{-- ============================================
         TOAST NOTIFICATIONS
         Notifikasi pop-up yang muncul ketika ada
         pesan sukses atau error dari server.
         ============================================ --}}
    @if(session('success'))
        <div class="toast toast-success" id="toast">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="toast toast-error" id="toast">{{ session('error') }}</div>
    @endif

    {{-- ============================================
         SIDEBAR (Menu Samping — Mobile Only)
         Muncul dari kiri saat hamburger ditekan.
         Berisi: logo, navigasi, toggle tema, profil.
         ============================================ --}}

    {{-- Overlay gelap di belakang sidebar --}}
    <div class="sidebar-backdrop" id="sidebar-backdrop"></div>

    <aside class="sidebar" id="sidebar">
        {{-- Header sidebar: logo + tombol X tutup --}}
        <div class="sidebar-header">
            <a href="{{ route('home') }}" class="logo">
                <i class="fa-solid fa-book-open"></i> Ceritaku
            </a>
            <button class="sidebar-close-btn" id="sidebar-close-btn" aria-label="Tutup Menu">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        {{-- Navigasi sidebar: menu yang sama dengan desktop --}}
        <nav class="sidebar-nav">
            <ul>
                @php $navItems = \App\Models\NavbarItem::active()->get(); @endphp
                @foreach($navItems as $nav)
                    <li>
                        <a href="{{ $nav->url }}" class="{{ request()->is(ltrim($nav->url, '/') ?: '/') ? 'active' : '' }}">
                            <i class="{{ $nav->icon }}"></i> {{ $nav->label }}
                        </a>
                    </li>
                @endforeach
            </ul>
        </nav>

        {{-- Bagian bawah sidebar: toggle tema + profil --}}
        <div class="sidebar-bottom">
            {{-- Tombol toggle tema (gelap/terang) di sidebar --}}
            <button id="sidebar-theme-toggle" class="theme-btn" aria-label="Toggle Theme">
                <i class="fa-solid fa-moon"></i> <span>Mode Gelap</span>
            </button>
            @auth
                {{-- Jika sudah login: tampilkan foto + nama --}}
                <a href="{{ route('dashboard') }}" class="sidebar-profile">
                    <img src="{{ asset(auth()->user()->avatar ?? 'img/p2.jpg') }}" alt="Profile">
                    <span>{{ auth()->user()->name }}</span>
                </a>
            @else
                {{-- Jika belum login: tampilkan link masuk --}}
                <a href="{{ route('login') }}" class="sidebar-profile">
                    <i class="fa-solid fa-right-to-bracket" style="font-size:1.2rem;color:var(--primary-color)"></i>
                    <span>Masuk</span>
                </a>
            @endauth
        </div>
    </aside>

    {{-- ============================================
         HEADER NAVIGATION (Navbar Utama)
         Tampil di semua halaman.
         Berisi: hamburger (mobile), logo, menu desktop,
         search bar, toggle tema, profil user.
         ============================================ --}}
    <header class="navbar" id="main-navbar">
        {{-- Tombol hamburger: toggle sidebar di mobile --}}
        <button class="hamburger-btn" id="hamburger-btn" aria-label="Menu">
            <i class="fa-solid fa-bars"></i>
        </button>

        {{-- Logo desktop --}}
        <a href="{{ route('home') }}" class="logo desktop-logo">
            <i class="fa-solid fa-book-open"></i> Ceritaku
        </a>

        {{-- Menu navigasi desktop --}}
        <nav class="desktop-nav">
            <ul>
                @foreach($navItems as $nav)
                    <li>
                        <a href="{{ $nav->url }}" class="{{ request()->is(ltrim($nav->url, '/') ?: '/') ? 'active' : '' }}">
                            {{ $nav->label }}
                        </a>
                    </li>
                @endforeach
            </ul>
        </nav>

        {{-- Area aksi di kanan navbar --}}
        <div class="nav-actions">
            {{-- Search bar --}}
            <form action="{{ route('search') }}" method="GET" class="search-bar">
                <input type="text" name="q" placeholder="Cari cerita favoritmu..." value="{{ request('q') }}">
                <button type="submit"><i class="fa-solid fa-search"></i></button>
            </form>

            {{-- Tombol toggle tema (bulan/matahari) — desktop only --}}
            <button id="theme-toggle" class="theme-btn desktop-only" aria-label="Toggle Theme">
                <i class="fa-solid fa-moon"></i>
            </button>

            @auth
                {{-- Menu profil user (dropdown) — desktop only --}}
                <div class="nav-user-menu desktop-only">
                    <div class="user-profile">
                        <img src="{{ asset(auth()->user()->avatar ?? 'img/p2.jpg') }}" alt="Profile">
                    </div>
                    <div class="nav-user-dropdown">
                        <a href="{{ route('dashboard') }}"><i class="fa-solid fa-gauge"></i> Dashboard</a>
                        <a href="{{ route('profile.edit') }}"><i class="fa-solid fa-user-gear"></i> Profil</a>
                        <div class="divider"></div>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit"><i class="fa-solid fa-right-from-bracket"></i> Keluar</button>
                        </form>
                    </div>
                </div>
            @else
                {{-- Tombol masuk — desktop only --}}
                <a href="{{ route('login') }}" class="btn btn-primary desktop-only" style="padding:0.5rem 1.2rem;font-size:0.9rem">
                    <i class="fa-solid fa-right-to-bracket"></i> Masuk
                </a>
            @endauth
        </div>
    </header>

    {{-- ============================================
         KONTEN UTAMA
         Di-inject oleh masing-masing halaman
         menggunakan @yield('content').
         ============================================ --}}
    @yield('content')

    {{-- ============================================
         REQUEST CERITA SECTION (di atas footer)
         Form untuk user mengirim permintaan cerita baru.
         Disembunyikan di halaman baca chapter.
         ============================================ --}}
    @if(!isset($hideRequest))
    <section class="request-section">
        <div class="container">
            <h3><i class="fa-solid fa-lightbulb"></i> Punya Ide Cerita?</h3>
            <p>Kirimkan permintaan ceritamu dan siapa tahu akan segera ditulis!</p>
            @auth
                <form action="{{ route('story-request.store') }}" method="POST" class="request-form">
                    @csrf
                    <input type="text" name="title" placeholder="Judul cerita yang kamu inginkan..." required>
                    <textarea name="description" placeholder="Deskripsi singkat (opsional)..."></textarea>
                    <button type="submit"><i class="fa-solid fa-paper-plane"></i> Kirim Request</button>
                </form>
            @else
                <p><a href="{{ route('login') }}" style="color:white;text-decoration:underline;font-weight:600">Login</a> atau <a href="{{ route('register') }}" style="color:white;text-decoration:underline;font-weight:600">buat akun</a> untuk mengirim request cerita.</p>
            @endauth
        </div>
    </section>
    @endif

    {{-- ============================================
         FOOTER
         Branding, link navigasi, kontak author.
         ============================================ --}}
    <footer id="main-footer">
        <div class="container footer-content">
            <div class="footer-brand">
                <i class="fa-solid fa-book-open"></i> Ceritaku
                <p>Platform membaca dan membagi kisah yang terinspirasi dari hidup.</p>
            </div>
            <div class="footer-links">
                <ul>
                    <li><strong>Eksplorasi</strong></li>
                    <li><a href="{{ route('explore') }}">Jelajahi</a></li>
                    <li><a href="{{ route('popular') }}">Populer</a></li>
                    <li><a href="{{ route('search') }}">Cari Cerita</a></li>
                </ul>
                <ul>
                    <li><strong>Akun</strong></li>
                    @auth
                        <li><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li><a href="{{ route('profile.edit') }}">Profil</a></li>
                    @else
                        <li><a href="{{ route('login') }}">Masuk</a></li>
                        <li><a href="{{ route('register') }}">Daftar</a></li>
                    @endauth
                </ul>
                <ul>
                    <li><strong>Hubungi Author</strong></li>
                    <li><a href="https://wa.me/6285707298084" target="_blank" title="085707298084"><i class="fa-brands fa-whatsapp"></i> WhatsApp</a></li>
                    <li><a href="https://instagram.com/artham_.26" target="_blank"><i class="fa-brands fa-instagram"></i> Instagram (@artham_.26)</a></li>
                    <li><a href="https://tiktok.com/@ad_ryuu" target="_blank"><i class="fa-brands fa-tiktok"></i> TikTok (@ad_ryuu)</a></li>
                    <li><span style="color:var(--text-muted);font-size:0.95rem;display:flex;align-items:center;gap:0.5rem;"><i class="fa-brands fa-discord"></i> Discord: artham_26</span></li>
                    <li style="margin-top: 0.5rem;">
                        <span style="font-size: 0.8rem; color: var(--text-muted); display: block; margin-bottom: 0.3rem;">Scan WA:</span>
                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=100x100&data=https://wa.me/6285707298084" alt="WA Barcode" style="border-radius: 8px; box-shadow: var(--shadow-sm); border: 2px solid white;">
                    </li>
                </ul>
            </div>
        </div>
        <div class="copyright">
            <p>&copy; {{ date('Y') }} Ceritaku. All rights reserved.</p>
        </div>
    </footer>

    {{-- ============================================
         JAVASCRIPT UTAMA
         Mengatur: tema (dark/light), sidebar, dropdown,
         anti-copy, toast auto-remove.
         ============================================ --}}
    <script>
        // ============================================
        // TEMA (DARK/LIGHT) — Fungsi Global
        // Fungsi ini dibuat global (window.*) agar bisa
        // dipanggil dari halaman reader juga.
        // ============================================

        const themeBtn = document.getElementById('theme-toggle');
        const sidebarThemeBtn = document.getElementById('sidebar-theme-toggle');
        const body = document.body;

        // Muat tema yang tersimpan di localStorage
        const savedTheme = localStorage.getItem('ceritaku-theme');
        if (savedTheme === 'dark') {
            body.classList.replace('light-theme', 'dark-theme');
            updateThemeIcons(true);
        }

        /**
         * Toggle tema antara light dan dark.
         * Menyimpan pilihan ke localStorage agar bertahan
         * saat halaman di-refresh.
         */
        function toggleTheme() {
            const isDark = body.classList.contains('light-theme');
            if (isDark) {
                body.classList.replace('light-theme', 'dark-theme');
                localStorage.setItem('ceritaku-theme', 'dark');
            } else {
                body.classList.replace('dark-theme', 'light-theme');
                localStorage.setItem('ceritaku-theme', 'light');
            }
            updateThemeIcons(isDark);
        }

        /**
         * Update icon tema di semua tempat:
         * - Tombol di navbar desktop (bulan/matahari)
         * - Tombol di sidebar mobile (bulan/matahari + teks)
         * @param {boolean} isDark - true jika sekarang mode gelap
         */
        function updateThemeIcons(isDark) {
            if (themeBtn) {
                const icon = themeBtn.querySelector('i');
                icon.className = isDark ? 'fa-solid fa-sun' : 'fa-solid fa-moon';
            }
            if (sidebarThemeBtn) {
                const icon = sidebarThemeBtn.querySelector('i');
                const text = sidebarThemeBtn.querySelector('span');
                icon.className = isDark ? 'fa-solid fa-sun' : 'fa-solid fa-moon';
                if (text) text.textContent = isDark ? 'Mode Terang' : 'Mode Gelap';
            }
        }

        /**
         * Set tema ke nilai tertentu (dipanggil dari reader).
         * @param {string} theme - 'dark' atau 'light'
         */
        function setTheme(theme) {
            if (theme === 'dark') {
                body.classList.remove('light-theme');
                body.classList.add('dark-theme');
                localStorage.setItem('ceritaku-theme', 'dark');
                updateThemeIcons(true);
            } else {
                body.classList.remove('dark-theme');
                body.classList.add('light-theme');
                localStorage.setItem('ceritaku-theme', 'light');
                updateThemeIcons(false);
            }
        }

        // Jadikan fungsi global agar bisa diakses dari reader.blade.php
        window.updateThemeIcons = updateThemeIcons;
        window.toggleTheme = toggleTheme;
        window.setTheme = setTheme;

        // Pasang event listener pada tombol-tombol tema
        if (themeBtn) themeBtn.addEventListener('click', toggleTheme);
        if (sidebarThemeBtn) sidebarThemeBtn.addEventListener('click', toggleTheme);

        // ============================================
        // SIDEBAR (Menu Samping Mobile)
        // Bisa dibuka/ditutup dengan:
        // 1. Tombol hamburger (☰) — toggle buka/tutup
        // 2. Tombol X di dalam sidebar
        // 3. Klik area gelap (backdrop)
        // ============================================

        const hamburgerBtn = document.getElementById('hamburger-btn');
        const sidebar = document.getElementById('sidebar');
        const sidebarBackdrop = document.getElementById('sidebar-backdrop');
        const sidebarCloseBtn = document.getElementById('sidebar-close-btn');

        /** Buka sidebar dan tampilkan backdrop */
        function openSidebar() {
            sidebar.classList.add('active');
            sidebarBackdrop.classList.add('active');
            body.style.overflow = 'hidden'; // Kunci scroll halaman
        }

        /** Tutup sidebar dan sembunyikan backdrop */
        function closeSidebar() {
            sidebar.classList.remove('active');
            sidebarBackdrop.classList.remove('active');
            body.style.overflow = ''; // Kembalikan scroll
        }

        /**
         * Toggle sidebar: jika terbuka → tutup, jika tertutup → buka.
         * Dipasang pada tombol hamburger.
         */
        function toggleSidebar() {
            if (sidebar.classList.contains('active')) {
                closeSidebar();
            } else {
                openSidebar();
            }
        }

        // Hamburger: toggle buka/tutup sidebar
        if (hamburgerBtn) hamburgerBtn.addEventListener('click', toggleSidebar);
        // Tombol X: tutup sidebar
        if (sidebarCloseBtn) sidebarCloseBtn.addEventListener('click', closeSidebar);
        // Klik backdrop: tutup sidebar
        if (sidebarBackdrop) sidebarBackdrop.addEventListener('click', closeSidebar);

        // ============================================
        // DROPDOWN PROFIL USER (Desktop — Navbar)
        // Klik foto profil untuk buka/tutup dropdown.
        // Klik di luar dropdown untuk menutup.
        // ============================================

        const profileImg = document.querySelector('.nav-user-menu img');
        const userDropdown = document.querySelector('.nav-user-dropdown');
        if (profileImg && userDropdown) {
            profileImg.addEventListener('click', (e) => {
                e.stopPropagation();
                userDropdown.classList.toggle('show');
            });
            document.addEventListener('click', (e) => {
                if (!userDropdown.contains(e.target)) {
                    userDropdown.classList.remove('show');
                }
            });
        }

        // ============================================
        // ANTI-COPY (Perlindungan Konten Cerita)
        // Mencegah klik kanan, salin teks, dan shortcut
        // keyboard pada elemen dengan class .no-copy.
        // ============================================

        document.querySelectorAll('.no-copy').forEach(el => {
            el.addEventListener('contextmenu', e => e.preventDefault());
            el.addEventListener('copy', e => e.preventDefault());
        });
        document.addEventListener('keydown', function(e) {
            if (document.querySelector('.no-copy:hover')) {
                if ((e.ctrlKey || e.metaKey) && ['c','a','u','s'].includes(e.key.toLowerCase())) {
                    e.preventDefault();
                }
            }
        });

        // ============================================
        // TOAST AUTO-REMOVE
        // Hapus notifikasi toast otomatis setelah 5 detik.
        // ============================================
        setTimeout(() => { const t = document.getElementById('toast'); if(t) t.remove(); }, 5000);
    </script>

    {{-- Stack untuk script tambahan dari halaman anak --}}
    @stack('scripts')
</body>
</html>
