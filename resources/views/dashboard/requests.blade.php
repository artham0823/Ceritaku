@extends('layouts.dashboard')
@section('title', 'Request Cerita - Ceritaku')

@section('content')
<div class="dash-header">
    <h1>Request Cerita</h1>
</div>

<div class="dash-card" style="overflow-x:auto">
    <table class="dash-table">
        <thead><tr><th>Pengguna</th><th>Judul Request</th><th>Deskripsi</th><th>Status</th><th>Tanggal</th><th>Aksi</th></tr></thead>
        <tbody>
            @forelse($requests as $req)
                <tr>
                    <td><strong>{{ $req->user->name }}</strong></td>
                    <td>{{ $req->title }}</td>
                    <td>{{ Str::limit($req->description, 100) }}</td>
                    <td>
                        <span class="status-badge status-{{ $req->status }}">
                            {{ $req->status === 'pending' ? 'Menunggu' : ($req->status === 'approved' ? 'Disetujui' : 'Ditolak') }}
                        </span>
                    </td>
                    <td><small>{{ $req->created_at->format('d M Y') }}</small></td>
                    <td>
                        @if($req->status === 'pending')
                        <div class="actions">
                            <form action="{{ route('dashboard.requests.update', $req->id) }}" method="POST">
                                @csrf @method('PUT')
                                <input type="hidden" name="status" value="approved">
                                <button type="submit" class="btn-edit"><i class="fa-solid fa-check"></i></button>
                            </form>
                            <form action="{{ route('dashboard.requests.update', $req->id) }}" method="POST">
                                @csrf @method('PUT')
                                <input type="hidden" name="status" value="rejected">
                                <button type="submit" class="btn-delete"><i class="fa-solid fa-xmark"></i></button>
                            </form>
                        </div>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="empty-state">Belum ada request cerita.</td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="pagination">{{ $requests->links() }}</div>
</div>
@endsection
