@extends('layouts.dashboard')
@section('title', 'Komentar - Ceritaku')

@section('content')
<div class="dash-header">
    <h1>Semua Komentar</h1>
</div>

<div class="dash-card" style="overflow-x:auto">
    <table class="dash-table">
        <thead><tr><th>Pengguna</th><th>Cerita</th><th>Bab</th><th>Komentar</th><th>Tanggal</th><th>Aksi</th></tr></thead>
        <tbody>
            @forelse($comments as $comment)
                <tr>
                    <td><strong><a href="{{ route('profile.show', $comment->user->id) }}" style="text-decoration:none; color:inherit;">{{ $comment->user->name }}</a></strong> <span class="comment-role {{ $comment->user->role }}">{{ ucfirst($comment->user->role) }}</span></td>
                    <td>{{ $comment->chapter->story->title ?? '-' }}</td>
                    <td>{{ $comment->chapter->title ?? '-' }}</td>
                    <td>{{ Str::limit($comment->content, 80) }}</td>
                    <td><small>{{ $comment->created_at->format('d M Y H:i') }}</small></td>
                    <td>
                        @if(auth()->user()->isAuthor() || (auth()->user()->isAdmin() && !$comment->user->isAuthor()))
                        <form action="{{ route('comment.destroy', $comment->id) }}" method="POST" onsubmit="return confirm('Hapus komentar ini?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn-delete"><i class="fa-solid fa-trash"></i></button>
                        </form>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="empty-state">Belum ada komentar.</td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="pagination">{{ $comments->links() }}</div>
</div>
@endsection
