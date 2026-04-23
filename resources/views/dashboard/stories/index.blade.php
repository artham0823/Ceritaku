@extends('layouts.dashboard')
@section('title', 'Kelola Cerita - Ceritaku')

@section('content')
<div class="dash-header">
    <div>
        <h1>Kelola Cerita</h1>
        <p>Tambah, edit, dan hapus cerita serta bab.</p>
    </div>
    <a href="{{ route('dashboard.stories.create') }}" class="btn btn-primary">
        <i class="fa-solid fa-plus"></i> Tambah Cerita
    </a>
</div>

<div class="dash-card" style="overflow-x:auto">
    <table class="dash-table">
        <thead>
            <tr>
                <th>Cover</th>
                <th>Judul</th>
                <th>Genre</th>
                <th>Bab</th>
                <th>Views</th>
                <th>Likes</th>
                <th>Diubah</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($stories as $story)
                <tr>
                    <td data-label="Cover"><img src="{{ asset($story->cover_image ?? 'img/p2.jpg') }}" alt="" style="width:40px;height:55px;object-fit:cover;border-radius:4px"></td>
                    <td data-label="Judul">
                        <div style="display: flex; align-items: center; flex-wrap: wrap;">
                            <strong>{{ $story->title }}</strong>
                            @if($story->is_featured) <span class="badge" style="font-size:0.65rem;padding:0.15rem 0.5rem;margin-left:0.3rem">Featured</span> @endif
                        </div>
                    </td>
                    <td data-label="Genre">{{ $story->genre }}</td>
                    <td data-label="Bab">{{ $story->chapters_count }}</td>
                    <td data-label="Views">{{ number_format($story->views_count) }}</td>
                    <td data-label="Likes">{{ $story->likes_count }}</td>
                    <td data-label="Diubah"><small>{{ $story->updated_at->format('d M Y') }}</small></td>
                    <td data-label="Aksi">
                        <div class="actions">
                            <a href="{{ route('dashboard.chapters.create', $story->id) }}" class="btn-edit" title="Tambah Bab"><i class="fa-solid fa-plus"></i> Tambah Bab</a>
                            <a href="{{ route('dashboard.stories.edit', $story->id) }}" class="btn-edit" title="Edit"><i class="fa-solid fa-pen"></i> Edit</a>
                            <form action="{{ route('dashboard.stories.destroy', $story->id) }}" method="POST" onsubmit="return confirm('Hapus cerita ini beserta semua babnya?')" style="margin:0;">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-delete" title="Hapus"><i class="fa-solid fa-trash"></i> Hapus</button>
                            </form>
                        </div>
                    </td>
                </tr>
                {{-- Chapter rows --}}
                @foreach($story->chapters as $chapter)
                    <tr style="background:var(--bg-accent)">
                        <td data-label=""></td>
                        <td data-label="Bab" style="padding-left:2rem"><small style="color:var(--text-muted)">↳</small> {{ $chapter->title }}</td>
                        <td data-label="Info" colspan="4"><small style="color:var(--text-muted)">Bab {{ $chapter->chapter_number }}</small></td>
                        <td data-label="Diubah"><small>{{ $chapter->updated_at->format('d M Y') }}</small></td>
                        <td data-label="Aksi">
                            <div class="actions">
                                <a href="{{ route('dashboard.chapters.edit', $chapter->id) }}" class="btn-edit" title="Edit Bab"><i class="fa-solid fa-pen"></i> Edit</a>
                                <form action="{{ route('dashboard.chapters.destroy', $chapter->id) }}" method="POST" onsubmit="return confirm('Hapus bab ini?')" style="margin:0;">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn-delete" title="Hapus Bab"><i class="fa-solid fa-trash"></i> Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
            @empty
                <tr><td colspan="8" class="empty-state" style="display: block; text-align: center;">Belum ada cerita. <a href="{{ route('dashboard.stories.create') }}" style="color:var(--primary-color)">Tambah cerita pertama!</a></td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
