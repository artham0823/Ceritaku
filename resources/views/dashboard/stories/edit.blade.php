@extends('layouts.dashboard')
@section('title', 'Edit Cerita - Ceritaku')

@section('content')
<div class="dash-header">
    <h1>Edit Cerita: {{ $story->title }}</h1>
</div>

<div class="dash-card">
    <form action="{{ route('dashboard.stories.update', $story->id) }}" method="POST" enctype="multipart/form-data" class="dash-form">
        @csrf @method('PUT')
        <div style="margin-bottom:1.5rem">
            <img src="{{ asset($story->cover_image ?? 'img/p2.jpg') }}" alt="" style="width:120px;height:170px;object-fit:cover;border-radius:var(--radius-sm)">
        </div>

        <div class="form-group">
            <label for="title">Judul Cerita *</label>
            <input type="text" id="title" name="title" value="{{ old('title', $story->title) }}" required>
            @error('title') <span class="form-error">{{ $message }}</span> @enderror
        </div>

        <div class="form-group">
            <label for="description">Deskripsi</label>
            <textarea id="description" name="description" rows="3">{{ old('description', $story->description) }}</textarea>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="genre">Genre</label>
                <input type="text" id="genre" name="genre" value="{{ old('genre', $story->genre) }}">
            </div>
            <div class="form-group">
                <label for="cover_image">Ganti Cover</label>
                <input type="file" id="cover_image" name="cover_image" accept="image/*">
                <p class="form-hint">Kosongkan jika tidak ingin mengubah.</p>
            </div>
        </div>

        <div class="form-group">
            <label class="form-check">
                <input type="checkbox" name="is_featured" value="1" {{ $story->is_featured ? 'checked' : '' }}>
                Cerita Utama (hero section)
            </label>
        </div>

        <div style="display:flex;gap:1rem">
            <button type="submit" class="btn btn-primary"><i class="fa-solid fa-save"></i> Simpan Perubahan</button>
            <a href="{{ route('dashboard.stories.index') }}" class="btn btn-outline">Batal</a>
        </div>
    </form>
</div>
@endsection
