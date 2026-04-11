@extends('layouts.dashboard')
@section('title', 'Tambah Cerita - Ceritaku')

@section('content')
<div class="dash-header">
    <h1>Tambah Cerita Baru</h1>
</div>

<div class="dash-card">
    <form action="{{ route('dashboard.stories.store') }}" method="POST" enctype="multipart/form-data" class="dash-form">
        @csrf
        <div class="form-group">
            <label for="title">Judul Cerita *</label>
            <input type="text" id="title" name="title" value="{{ old('title') }}" placeholder="Masukkan judul cerita" required>
            @error('title') <span class="form-error">{{ $message }}</span> @enderror
        </div>

        <div class="form-group">
            <label for="description">Deskripsi</label>
            <textarea id="description" name="description" rows="3" placeholder="Deskripsi singkat tentang cerita ini...">{{ old('description') }}</textarea>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="genre">Genre</label>
                <input type="text" id="genre" name="genre" value="{{ old('genre') }}" placeholder="Contoh: Fantasy, Action">
                <p class="form-hint">Pisahkan dengan koma untuk multi-genre.</p>
            </div>
            <div class="form-group">
                <label for="cover_image">Cover Cerita</label>
                <input type="file" id="cover_image" name="cover_image" accept="image/*">
                <p class="form-hint">Format: JPG, PNG, WebP. Maks 5MB.</p>
            </div>
        </div>

        <div class="form-group">
            <label class="form-check">
                <input type="checkbox" name="is_featured" value="1" {{ old('is_featured') ? 'checked' : '' }}>
                Tampilkan sebagai cerita utama (di hero section)
            </label>
        </div>

        <div style="display:flex;gap:1rem">
            <button type="submit" class="btn btn-primary"><i class="fa-solid fa-save"></i> Simpan Cerita</button>
            <a href="{{ route('dashboard.stories.index') }}" class="btn btn-outline">Batal</a>
        </div>
    </form>
</div>
@endsection
