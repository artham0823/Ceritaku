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
                    <td><img src="{{ asset($story->cover_image ?? 'img/p2.jpg') }}" alt="" style="width:40px;height:55px;object-fit:cover;border-radius:4px"></td>
                    <td>
                        <strong>{{ $story->title }}</strong>
                        @if($story->is_featured) <span class="badge" style="font-size:0.65rem;padding:0.15rem 0.5rem;margin-left:0.3rem">Featured</span> @endif
                    </td>
                    <td>{{ $story->genre }}</td>
                    <td>{{ $story->chapters_count }}</td>
                    <td>{{ number_format($story->views_count) }}</td>
                    <td>{{ $story->likes_count }}</td>
                    <td><small>{{ $story->updated_at->format('d M Y') }}</small></td>
                    <td>
                        <div class="actions">
                            <a href="{{ route('dashboard.chapters.create', $story->id) }}" class="btn-edit" title="Tambah Bab"><i class="fa-solid fa-plus"></i></a>
                            <a href="{{ route('dashboard.stories.edit', $story->id) }}" class="btn-edit" title="Edit"><i class="fa-solid fa-pen"></i></a>
                            <form action="{{ route('dashboard.stories.destroy', $story->id) }}" method="POST" onsubmit="return confirm('Hapus cerita ini beserta semua babnya?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-delete" title="Hapus"><i class="fa-solid fa-trash"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                {{-- Chapter rows --}}
                @foreach($story->chapters as $chapter)
                    <tr style="background:var(--bg-accent)">
                        <td></td>
                        <td style="padding-left:2rem"><small style="color:var(--text-muted)">↳</small> {{ $chapter->title }}</td>
                        <td colspan="4"><small style="color:var(--text-muted)">Bab {{ $chapter->chapter_number }}</small></td>
                        <td><small>{{ $chapter->updated_at->format('d M Y') }}</small></td>
                        <td>
                            <div class="actions">
                                <a href="{{ route('dashboard.chapters.edit', $chapter->id) }}" class="btn-edit" title="Edit Bab"><i class="fa-solid fa-pen"></i></a>
                                <form action="{{ route('dashboard.chapters.destroy', $chapter->id) }}" method="POST" onsubmit="return confirm('Hapus bab ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn-delete" title="Hapus Bab"><i class="fa-solid fa-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
            @empty
                <tr><td colspan="8" class="empty-state">Belum ada cerita. <a href="{{ route('dashboard.stories.create') }}" style="color:var(--primary-color)">Tambah cerita pertama!</a></td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
