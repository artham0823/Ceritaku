@extends('layouts.dashboard')
@section('title', 'Edit Bab - Ceritaku')

@section('content')
<div class="dash-header">
    <h1>Edit Bab: {{ $chapter->title }}</h1>
    <p>Cerita: {{ $chapter->story->title }}</p>
</div>

<div class="dash-card">
    <form action="{{ route('dashboard.chapters.update', $chapter->id) }}" method="POST" class="dash-form">
        @csrf @method('PUT')
        <div class="form-group">
            <label for="title">Judul Bab *</label>
            <input type="text" id="title" name="title" value="{{ old('title', $chapter->title) }}" required>
        </div>

        <label>Isi Bab *</label>
        <div class="editor-toolbar">
            <button type="button" onclick="insertTemplate('narration')"><i class="fa-solid fa-paragraph"></i> Narasi</button>
            <button type="button" onclick="insertTemplate('dialogue')"><i class="fa-solid fa-comment"></i> Dialog</button>
            <button type="button" onclick="insertTemplate('scene')"><i class="fa-solid fa-location-dot"></i> Scene</button>
            <button type="button" onclick="insertTemplate('character')"><i class="fa-solid fa-user"></i> Karakter</button>
            <button type="button" onclick="insertTemplate('break')"><i class="fa-solid fa-minus"></i> Pemisah</button>
        </div>
        <div class="form-group" style="margin-top:0">
            <textarea id="content" name="content" rows="25" style="border-radius:0 0 var(--radius-sm) var(--radius-sm)">{{ old('content', $chapter->content) }}</textarea>
            @error('content') <span class="form-error">{{ $message }}</span> @enderror
        </div>

        <div style="display:flex;gap:1rem">
            <button type="submit" class="btn btn-primary"><i class="fa-solid fa-save"></i> Simpan Perubahan</button>
            <a href="{{ route('dashboard.stories.index') }}" class="btn btn-outline">Batal</a>
        </div>
    </form>
</div>

@push('scripts')
<script>
function insertTemplate(type) {
    const textarea = document.getElementById('content');
    const templates = {
        narration: '<div class="narration">Tulis narasi di sini...</div>\n',
        dialogue: '<div class="dialogue"><span class="speaker">Nama Karakter</span><span class="speech">"Tulis dialog di sini..."</span></div>\n',
        scene: '<div class="scene-setting">Lokasi, waktu. Deskripsi suasana.</div>\n',
        character: '<div class="character-intro"><div class="char-name">Nama Karakter</div><div class="char-desc">Deskripsi singkat karakter.</div></div>\n',
        break: '<div class="scene-break">◆ ◆ ◆</div>\n',
    };
    const template = templates[type] || '';
    const start = textarea.selectionStart;
    textarea.value = textarea.value.substring(0, start) + template + textarea.value.substring(textarea.selectionEnd);
    textarea.focus();
}
</script>
@endpush
@endsection
