@extends('layouts.app')
@section('title', $chapter->title . ' - ' . $story->title)
@push('styles')
<link rel="stylesheet" href="{{ asset('css/reader.css') }}">
@endpush

@php $hideRequest = true; @endphp

@section('content')
<div class="reader-page">
    {{-- Reader Navbar --}}
    <header class="reader-navbar">
        <a href="{{ route('story.show', $story->id) }}" class="reader-back-btn">
            <i class="fa-solid fa-arrow-left"></i>
            <span>Kembali</span>
        </a>
        <div class="reader-title-mini">{{ $chapter->title }}</div>
        <span class="reader-progress">{{ $chapter->chapter_number }} / {{ $story->chapters->count() }}</span>
    </header>

    {{-- Reader Content (Anti-copy) --}}
    <div class="reader-content no-copy">
        <div class="reader-chapter-title">{{ $chapter->title }}</div>
        <div class="reader-chapter-body">
            {!! $chapter->content !!}
        </div>
    </div>

    {{-- Navigation --}}
    <div class="reader-nav">
        @if($prevChapter)
            <a href="{{ route('chapter.show', [$story->id, $prevChapter->id]) }}" class="nav-prev">
                <i class="fa-solid fa-chevron-left"></i> Sebelumnya
            </a>
        @else
            <span class="nav-disabled"><i class="fa-solid fa-chevron-left"></i> Sebelumnya</span>
        @endif

        <span class="reader-progress">Bab {{ $chapter->chapter_number }} dari {{ $story->chapters->count() }}</span>

        @if($nextChapter)
            <a href="{{ route('chapter.show', [$story->id, $nextChapter->id]) }}" class="nav-next">
                Selanjutnya <i class="fa-solid fa-chevron-right"></i>
            </a>
        @else
            <span class="nav-disabled">Selanjutnya <i class="fa-solid fa-chevron-right"></i></span>
        @endif
    </div>

    {{-- Comment Section --}}
    <div class="container" style="max-width:750px;margin:2rem auto;padding-bottom:3rem">
        <div class="comment-section">
            <h3><i class="fa-solid fa-comments"></i> Komentar ({{ $comments->count() }})</h3>

            @auth
                @if(auth()->user()->canComment())
                    <div class="comment-form">
                        <form action="{{ route('comment.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="chapter_id" value="{{ $chapter->id }}">
                            <textarea name="content" placeholder="Tulis komentar..." required>{{ old('content') }}</textarea>
                            <div class="comment-actions">
                                <span class="comment-remaining">Sisa komentar hari ini: {{ auth()->user()->remainingComments() }}</span>
                                <button type="submit" class="btn btn-primary" style="padding:0.5rem 1.2rem;font-size:0.9rem">
                                    <i class="fa-solid fa-paper-plane"></i> Kirim
                                </button>
                            </div>
                        </form>
                    </div>
                @else
                    <div class="login-prompt">
                        <p>Anda sudah mencapai batas komentar hari ini. Coba lagi besok!</p>
                    </div>
                @endif
            @else
                <div class="login-prompt">
                    <p><a href="{{ route('login') }}">Login</a> atau <a href="{{ route('register') }}">buat akun</a> untuk berkomentar.</p>
                </div>
            @endauth

            @foreach($comments as $comment)
                <div class="comment-item">
                    <img src="{{ asset($comment->user->avatar ?? 'img/p2.jpg') }}" alt="" class="comment-avatar">
                    <div class="comment-body">
                        <div class="comment-header">
                            <div>
                                <span class="comment-name">{{ $comment->user->name }}</span>
                                <span class="comment-role {{ $comment->user->role }}">{{ ucfirst($comment->user->role) }}</span>
                            </div>
                            <span class="comment-date">{{ $comment->created_at->diffForHumans() }}</span>
                        </div>
                        <p class="comment-text">{{ $comment->content }}</p>
                        @auth
                            @if(
                                auth()->user()->isAuthor() ||
                                (auth()->user()->isAdmin() && !$comment->user->isAuthor()) ||
                                auth()->id() === $comment->user_id
                            )
                                <form action="{{ route('comment.destroy', $comment->id) }}" method="POST" style="margin-top:0.3rem" onsubmit="return confirm('Hapus komentar ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="comment-delete"><i class="fa-solid fa-trash"></i> Hapus</button>
                                </form>
                            @endif
                        @endauth
                    </div>
                </div>
            @endforeach

            @if($comments->isEmpty())
                <div class="empty-state">
                    <p>Belum ada komentar. Jadilah yang pertama!</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
