{{-- =====================================================
     HALAMAN EDIT PROFIL (Dashboard)
     =====================================================
     Form untuk mengedit profil user yang sedang login.
     
     Fitur:
     - Upload foto profil (avatar)
     - Ubah nama dan bio
     - Ubah title/gelar (khusus author)
     - Kelola link sosial media / game ID
     ===================================================== --}}

@extends('layouts.dashboard')
@section('title', 'Profil - Ceritaku')

@section('content')
<div class="dash-header">
    <h1>Pengaturan Profil</h1>
</div>

{{-- ============================================
     FORM EDIT PROFIL
     ============================================ --}}
<div class="dash-card">
    <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" class="dash-form">
        @csrf @method('PUT')

        {{-- Preview avatar saat ini --}}
        <div style="text-align:center;margin-bottom:2rem">
            <img src="{{ asset(auth()->user()->avatar ?? 'img/p2.jpg') }}" alt="Avatar" style="width:100px;height:100px;border-radius:50%;object-fit:cover;border:3px solid var(--primary-color);margin-bottom:1rem">
        </div>

        {{-- Upload foto profil baru --}}
        <div class="form-group">
            <label for="avatar">Foto Profil</label>
            <input type="file" id="avatar" name="avatar" accept="image/*">
            <p class="form-hint">Format: JPG, PNG, WebP. Maksimal 2MB.</p>
            @error('avatar') <span class="form-error">{{ $message }}</span> @enderror
        </div>

        {{-- Nama tampilan --}}
        <div class="form-group">
            <label for="name">Nama</label>
            <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required>
            @error('name') <span class="form-error">{{ $message }}</span> @enderror
        </div>

        {{-- Title/Gelar (hanya tampil untuk author) --}}
        @if($user->isAuthor())
        <div class="form-group">
            <label for="title">Title / Gelar</label>
            <input type="text" id="title" name="title" value="{{ old('title', $user->title) }}" placeholder="Contoh: Penulis Utama">
        </div>
        @endif

        {{-- Bio / Deskripsi singkat --}}
        <div class="form-group">
            <label for="bio">Bio / Deskripsi Singkat</label>
            <textarea id="bio" name="bio" rows="3" placeholder="Ceritakan sedikit tentang dirimu..." maxlength="500">{{ old('bio', $user->bio) }}</textarea>
            <p class="form-hint">Maksimal 500 karakter.</p>
            @error('bio') <span class="form-error">{{ $message }}</span> @enderror
        </div>

        {{-- Username (tidak bisa diubah) --}}
        <div class="form-group">
            <label>Username</label>
            <input type="text" value="{{ $user->username }}" disabled style="opacity:0.6">
            <p class="form-hint">Username tidak bisa diubah.</p>
        </div>

        {{-- Role (tidak bisa diubah) --}}
        <div class="form-group">
            <label>Role</label>
            <input type="text" value="{{ ucfirst($user->role) }}" disabled style="opacity:0.6">
        </div>

        <button type="submit" class="btn btn-primary">
            <i class="fa-solid fa-save"></i> Simpan Perubahan
        </button>
    </form>
</div>

{{-- ============================================
     SOSIAL MEDIA / GAME ID
     User bisa menambahkan link sosmed atau game ID
     yang akan ditampilkan di profil publiknya.
     
     Batas: Member max 10, Admin & Author unlimited.
     ============================================ --}}
<div class="dash-card" style="margin-top: 2rem;">
    <h2 style="font-size: 1.3rem; margin-bottom: 0.5rem;">
        <i class="fa-solid fa-share-nodes"></i> Sosial Media & Game ID
    </h2>
    <p style="color: var(--text-muted); font-size: 0.9rem; margin-bottom: 1.5rem;">
        Tambahkan link sosial media atau ID game yang ingin kamu tampilkan di profilmu.
        @if($user->isMember())
            (Maksimal 10 item)
        @else
            (Tanpa batas)
        @endif
    </p>

    {{-- Daftar sosmed yang sudah ditambahkan --}}
    @if($socialLinks->count() > 0)
    <div style="margin-bottom: 1.5rem;">
        @foreach($socialLinks as $link)
            <div style="display: flex; align-items: center; justify-content: space-between; padding: 0.7rem 1rem; background: var(--bg-accent); border-radius: var(--radius-sm); margin-bottom: 0.5rem; border: 1px solid var(--border-color);">
                {{-- Info sosmed: icon + label + value --}}
                <div style="display: flex; align-items: center; gap: 0.6rem; flex: 1; min-width: 0;">
                    <i class="{{ $link->icon }}" style="color: var(--primary-color); font-size: 1rem; width: 1.2rem; text-align: center;"></i>
                    <span style="font-weight: 600; font-size: 0.9rem;">{{ $link->label }}</span>
                    <span style="color: var(--text-muted); font-size: 0.85rem; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">{{ $link->value }}</span>
                </div>
                {{-- Tombol hapus --}}
                <form action="{{ route('profile.social.destroy', $link->id) }}" method="POST" onsubmit="return confirm('Hapus link ini?')" style="flex-shrink: 0;">
                    @csrf @method('DELETE')
                    <button type="submit" style="background: none; border: none; color: var(--text-muted); cursor: pointer; padding: 0.3rem; font-size: 0.9rem; transition: color 0.3s;" onmouseover="this.style.color='var(--danger)'" onmouseout="this.style.color='var(--text-muted)'">
                        <i class="fa-solid fa-trash"></i>
                    </button>
                </form>
            </div>
        @endforeach
    </div>
    @endif

    {{-- Form tambah sosmed baru --}}
    @php
        $currentCount = $socialLinks->count();
        $limit = $user->getSocialLinksLimit();
        $canAdd = $currentCount < $limit;
    @endphp

    @if($canAdd)
    <form action="{{ route('profile.social.store') }}" method="POST" class="dash-form">
        @csrf
        <div style="display: flex; gap: 0.8rem; flex-wrap: wrap; align-items: flex-end;">
            {{-- Dropdown pilih icon --}}
            <div class="form-group" style="margin-bottom: 0; min-width: 160px; flex: 1;">
                <label for="social-icon" style="font-size: 0.85rem;">Icon</label>
                <select id="social-icon" name="icon" required style="width: 100%; padding: 0.6rem 0.8rem; border: 1px solid var(--border-color); border-radius: var(--radius-sm); background: var(--bg-card); color: var(--text-main); font-family: 'Outfit', sans-serif; font-size: 0.9rem;">
                    <option value="">-- Pilih Icon --</option>
                    {{-- Sosial media populer --}}
                    <optgroup label="Sosial Media">
                        <option value="fa-brands fa-instagram">📸 Instagram</option>
                        <option value="fa-brands fa-tiktok">🎵 TikTok</option>
                        <option value="fa-brands fa-x-twitter">🐦 X / Twitter</option>
                        <option value="fa-brands fa-facebook">📘 Facebook</option>
                        <option value="fa-brands fa-youtube">🎬 YouTube</option>
                        <option value="fa-brands fa-whatsapp">💬 WhatsApp</option>
                        <option value="fa-brands fa-telegram">✈️ Telegram</option>
                        <option value="fa-brands fa-discord">🎮 Discord</option>
                        <option value="fa-brands fa-threads">🧵 Threads</option>
                        <option value="fa-brands fa-linkedin">💼 LinkedIn</option>
                        <option value="fa-brands fa-pinterest">📌 Pinterest</option>
                        <option value="fa-brands fa-snapchat">👻 Snapchat</option>
                        <option value="fa-brands fa-reddit">🔴 Reddit</option>
                        <option value="fa-brands fa-twitch">🟣 Twitch</option>
                        <option value="fa-brands fa-line">🟢 LINE</option>
                        <option value="fa-brands fa-weixin">💚 WeChat</option>
                    </optgroup>
                    {{-- Developer / Portfolio --}}
                    <optgroup label="Developer">
                        <option value="fa-brands fa-github">💻 GitHub</option>
                        <option value="fa-brands fa-gitlab">🦊 GitLab</option>
                        <option value="fa-brands fa-stack-overflow">📚 Stack Overflow</option>
                        <option value="fa-brands fa-codepen">🖊️ CodePen</option>
                        <option value="fa-brands fa-dev">🧑‍💻 DEV.to</option>
                        <option value="fa-solid fa-globe">🌐 Website</option>
                        <option value="fa-solid fa-link">🔗 Link</option>
                    </optgroup>
                    {{-- Gaming --}}
                    <optgroup label="Gaming">
                        <option value="fa-brands fa-steam">🎮 Steam</option>
                        <option value="fa-brands fa-xbox">🟩 Xbox</option>
                        <option value="fa-brands fa-playstation">🎮 PlayStation</option>
                        <option value="fa-brands fa-battle-net">⚔️ Battle.net</option>
                        <option value="fa-solid fa-gamepad">🕹️ Game ID</option>
                        <option value="fa-solid fa-headset">🎧 Nickname Game</option>
                    </optgroup>
                    {{-- Lainnya --}}
                    <optgroup label="Lainnya">
                        <option value="fa-solid fa-envelope">📧 Email</option>
                        <option value="fa-solid fa-phone">📱 Telepon</option>
                        <option value="fa-solid fa-map-marker-alt">📍 Lokasi</option>
                        <option value="fa-solid fa-music">🎵 Spotify / Musik</option>
                        <option value="fa-solid fa-star">⭐ Custom</option>
                    </optgroup>
                </select>
                @error('icon') <span class="form-error">{{ $message }}</span> @enderror
            </div>

            {{-- Input label (nama tampilan) --}}
            <div class="form-group" style="margin-bottom: 0; min-width: 120px; flex: 1;">
                <label for="social-label" style="font-size: 0.85rem;">Label</label>
                <input type="text" id="social-label" name="label" placeholder="Instagram" required maxlength="100" style="padding: 0.6rem 0.8rem; border: 1px solid var(--border-color); border-radius: var(--radius-sm); background: var(--bg-card); color: var(--text-main); font-family: 'Outfit', sans-serif; font-size: 0.9rem; width: 100%;">
                @error('label') <span class="form-error">{{ $message }}</span> @enderror
            </div>

            {{-- Input value (link / username / ID) --}}
            <div class="form-group" style="margin-bottom: 0; min-width: 180px; flex: 2;">
                <label for="social-value" style="font-size: 0.85rem;">Link / ID</label>
                <input type="text" id="social-value" name="value" placeholder="https://instagram.com/username atau ID Game" required maxlength="255" style="padding: 0.6rem 0.8rem; border: 1px solid var(--border-color); border-radius: var(--radius-sm); background: var(--bg-card); color: var(--text-main); font-family: 'Outfit', sans-serif; font-size: 0.9rem; width: 100%;">
                @error('value') <span class="form-error">{{ $message }}</span> @enderror
            </div>

            {{-- Tombol tambah --}}
            <button type="submit" class="btn btn-primary" style="padding: 0.6rem 1.2rem; font-size: 0.9rem; white-space: nowrap; margin-bottom: 0;">
                <i class="fa-solid fa-plus"></i> Tambah
            </button>
        </div>
    </form>
    @else
        <p style="color: var(--text-muted); font-style: italic; text-align: center; padding: 1rem;">
            Anda sudah mencapai batas maksimal link sosial media.
        </p>
    @endif
</div>
@endsection
