@extends('layouts.dashboard')
@section('title', 'Profil - Ceritaku')

@section('content')
<div class="dash-header">
    <h1>Pengaturan Profil</h1>
</div>

<div class="dash-card">
    <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" class="dash-form">
        @csrf @method('PUT')

        <div style="text-align:center;margin-bottom:2rem">
            <img src="{{ asset(auth()->user()->avatar ?? 'img/p2.jpg') }}" alt="Avatar" style="width:100px;height:100px;border-radius:50%;object-fit:cover;border:3px solid var(--primary-color);margin-bottom:1rem">
        </div>

        <div class="form-group">
            <label for="avatar">Foto Profil</label>
            <input type="file" id="avatar" name="avatar" accept="image/*">
            <p class="form-hint">Format: JPG, PNG, WebP. Maksimal 2MB.</p>
            @error('avatar') <span class="form-error">{{ $message }}</span> @enderror
        </div>

        <div class="form-group">
            <label for="name">Nama</label>
            <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required>
            @error('name') <span class="form-error">{{ $message }}</span> @enderror
        </div>

        @if($user->isAuthor())
        <div class="form-group">
            <label for="title">Title / Gelar</label>
            <input type="text" id="title" name="title" value="{{ old('title', $user->title) }}" placeholder="Contoh: Penulis Utama">
        </div>
        @endif

        <div class="form-group">
            <label for="bio">Bio / Deskripsi Singkat</label>
            <textarea id="bio" name="bio" rows="3" placeholder="Ceritakan sedikit tentang dirimu..." maxlength="500">{{ old('bio', $user->bio) }}</textarea>
            <p class="form-hint">Maksimal 500 karakter.</p>
            @error('bio') <span class="form-error">{{ $message }}</span> @enderror
        </div>

        <div class="form-group">
            <label>Username</label>
            <input type="text" value="{{ $user->username }}" disabled style="opacity:0.6">
            <p class="form-hint">Username tidak bisa diubah.</p>
        </div>

        <div class="form-group">
            <label>Role</label>
            <input type="text" value="{{ ucfirst($user->role) }}" disabled style="opacity:0.6">
        </div>

        <button type="submit" class="btn btn-primary">
            <i class="fa-solid fa-save"></i> Simpan Perubahan
        </button>
    </form>
</div>
@endsection
