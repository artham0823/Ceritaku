<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard - Ceritaku')</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    @if(auth()->user()->isAuthor())
        <link rel="stylesheet" href="{{ asset('css/dashboard-author.css') }}">
    @elseif(auth()->user()->isAdmin())
        <link rel="stylesheet" href="{{ asset('css/dashboard-admin.css') }}">
    @else
        <link rel="stylesheet" href="{{ asset('css/dashboard-member.css') }}">
    @endif
    @stack('styles')
</head>
<body class="light-theme">
    {{-- Toast --}}
    @if(session('success'))
        <div class="toast toast-success" id="toast">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="toast toast-error" id="toast">{{ session('error') }}</div>
    @endif

    <div class="dash-layout">
        {{-- Dashboard Sidebar --}}
        <aside class="dash-sidebar" id="dash-sidebar">
            <div class="dash-sidebar-header">
                <a href="{{ route('home') }}" class="logo">
                    <i class="fa-solid fa-book-open"></i> Ceritaku
                </a>
            </div>
            <div class="dash-sidebar-user">
                <img src="{{ asset(auth()->user()->avatar ?? 'img/p2.jpg') }}" alt="Avatar">
                <div class="user-info">
                    <h4>{{ auth()->user()->name }}</h4>
                    <span class="role-badge-{{ auth()->user()->role }}">{{ ucfirst(auth()->user()->role) }}</span>
                </div>
            </div>
            <nav class="dash-sidebar-nav">
                <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i class="fa-solid fa-gauge"></i> Dashboard
                </a>
                <a href="{{ route('profile.edit') }}" class="{{ request()->routeIs('profile.edit') ? 'active' : '' }}">
                    <i class="fa-solid fa-user-gear"></i> Profil
                </a>
                <a href="{{ route('dashboard.history') }}" class="{{ request()->routeIs('dashboard.history') ? 'active' : '' }}">
                    <i class="fa-solid fa-clock-rotate-left"></i> Riwayat Bacaan
                </a>

                @if(auth()->user()->canManageContent())
                    <div class="nav-section">Konten</div>
                    <a href="{{ route('dashboard.stories.index') }}" class="{{ request()->routeIs('dashboard.stories.*') || request()->routeIs('dashboard.chapters.*') ? 'active' : '' }}">
                        <i class="fa-solid fa-book"></i> Kelola Cerita
                    </a>
                    <a href="{{ route('dashboard.comments') }}" class="{{ request()->routeIs('dashboard.comments') ? 'active' : '' }}">
                        <i class="fa-solid fa-comments"></i> Komentar
                    </a>
                    <a href="{{ route('dashboard.requests') }}" class="{{ request()->routeIs('dashboard.requests') ? 'active' : '' }}">
                        <i class="fa-solid fa-inbox"></i> Request Cerita
                    </a>

                    <div class="nav-section">Pengguna</div>
                    <a href="{{ route('dashboard.members') }}" class="{{ request()->routeIs('dashboard.members') ? 'active' : '' }}">
                        <i class="fa-solid fa-users"></i> Daftar Member
                    </a>
                @endif

                @if(auth()->user()->isAuthor())
                    <a href="{{ route('dashboard.admins') }}" class="{{ request()->routeIs('dashboard.admins') ? 'active' : '' }}">
                        <i class="fa-solid fa-user-shield"></i> Kelola Admin
                    </a>
                    <div class="nav-section">Pengaturan</div>
                    <a href="{{ route('dashboard.navbar') }}" class="{{ request()->routeIs('dashboard.navbar') ? 'active' : '' }}">
                        <i class="fa-solid fa-bars"></i> Kelola Navbar
                    </a>
                    <a href="{{ route('dashboard.notifications') }}" class="{{ request()->routeIs('dashboard.notifications') ? 'active' : '' }}">
                        <i class="fa-solid fa-bell"></i> Notifikasi
                    </a>
                @endif
            </nav>
            <div class="dash-sidebar-footer">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-outline" style="width:100%;justify-content:center">
                        <i class="fa-solid fa-right-from-bracket"></i> Keluar
                    </button>
                </form>
            </div>
        </aside>

        {{-- Dashboard Content --}}
        <main class="dash-content">
            @yield('content')
        </main>
    </div>

    {{-- Mobile sidebar toggle --}}
    <button class="dash-mobile-toggle" id="dash-mobile-toggle">
        <i class="fa-solid fa-bars"></i>
    </button>

    <script>
        // Theme
        const saved = localStorage.getItem('ceritaku-theme');
        if (saved === 'dark') document.body.classList.replace('light-theme', 'dark-theme');

        // Mobile sidebar
        const toggle = document.getElementById('dash-mobile-toggle');
        const dashSidebar = document.getElementById('dash-sidebar');
        if (toggle) {
            toggle.addEventListener('click', () => {
                dashSidebar.classList.toggle('active');
            });
        }

        // Auto-remove toast
        setTimeout(() => { const t = document.getElementById('toast'); if(t) t.remove(); }, 5000);
    </script>
    @stack('scripts')
</body>
</html>
