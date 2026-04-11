@extends('layouts.dashboard')
@section('title', 'Tambah Bab - Ceritaku')

@section('content')
<div class="dash-header">
    <h1>Tambah Bab Baru — {{ $story->title }}</h1>
</div>

<div class="dash-card">
    <form action="{{ route('dashboard.chapters.store', $story->id) }}" method="POST" class="dash-form">
        @csrf
        <div class="form-row">
            <div class="form-group">
                <label for="title">Judul Bab *</label>
                <input type="text" id="title" name="title" value="{{ old('title') }}" placeholder="Contoh: Bab 1: Awal Mula" required>
                @error('title') <span class="form-error">{{ $message }}</span> @enderror
            </div>
            <div class="form-group">
                <label for="chapter_number">Nomor Bab *</label>
                <input type="number" id="chapter_number" name="chapter_number" value="{{ old('chapter_number', $nextNumber) }}" min="1" required>
            </div>
        </div>

        {{-- Toolbar untuk menambahkan elemen cerita --}}
        <label>Isi Bab *</label>
        <div class="editor-toolbar">
            <button type="button" onclick="insertTemplate('narration')" title="Tambah narasi"><i class="fa-solid fa-paragraph"></i> Narasi</button>
            <button type="button" onclick="insertTemplate('dialogue')" title="Tambah dialog"><i class="fa-solid fa-comment"></i> Dialog</button>
            <button type="button" onclick="insertTemplate('scene')" title="Tambah scene setting"><i class="fa-solid fa-location-dot"></i> Scene</button>
            <button type="button" onclick="insertTemplate('character')" title="Tambah karakter"><i class="fa-solid fa-user"></i> Karakter</button>
            <button type="button" onclick="insertTemplate('break')" title="Tambah pemisah"><i class="fa-solid fa-minus"></i> Pemisah</button>
        </div>
        <div class="form-group" style="margin-top:0">
            <textarea id="content" name="content" rows="20" placeholder="Tulis isi bab di sini... Bisa menggunakan tombol di atas untuk menambahkan format dialog, narasi, dll." style="border-radius:0 0 var(--radius-sm) var(--radius-sm)">{{ old('content') }}</textarea>
            <p class="form-hint">Gunakan tombol di atas untuk menambahkan format dialog, narasi, scene setting, dll. Konten mendukung HTML.</p>
            @error('content') <span class="form-error">{{ $message }}</span> @enderror
        </div>

        <div style="display:flex;gap:1rem">
            <button type="submit" class="btn btn-primary"><i class="fa-solid fa-save"></i> Simpan Bab</button>
            <a href="{{ route('dashboard.stories.index') }}" class="btn btn-outline">Batal</a>
        </div>
    </form>
</div>

@push('scripts')
<script>
// Template HTML untuk elemen cerita
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
    const end = textarea.selectionEnd;
    textarea.value = textarea.value.substring(0, start) + template + textarea.value.substring(end);
    textarea.focus();
    textarea.setSelectionRange(start + template.length, start + template.length);
}
</script>
@endpush
@endsection
