@extends('layouts.dashboard')
@section('title', 'Daftar Member - Ceritaku')

@section('content')
<div class="dash-header">
    <h1>Daftar Member</h1>
</div>

<div class="dash-card" style="overflow-x:auto">
    <table class="dash-table">
        <thead><tr><th>Avatar</th><th>Nama</th><th>Status</th><th>Bergabung</th><th>Aksi</th></tr></thead>
        <tbody>
            @forelse($members as $member)
                <tr>
                    <td data-label="Avatar"><img src="{{ asset($member->avatar ?? 'img/p2.jpg') }}" alt="" style="width:35px;height:35px;border-radius:50%;object-fit:cover"></td>
                    <td data-label="Nama"><strong><a href="{{ route('profile.show', $member->id) }}" style="text-decoration:none; color:inherit;">{{ $member->name }}</a></strong></td>
                    <td data-label="Status">
                        @if($member->is_blocked)
                            <span class="status-badge status-blocked">Diblokir</span>
                        @else
                            <span class="status-badge status-active">Aktif</span>
                        @endif
                    </td>
                    <td data-label="Bergabung"><small>{{ $member->created_at->format('d M Y') }}</small></td>
                    <td data-label="Aksi">
                        <form action="{{ route('dashboard.users.block', $member->id) }}" method="POST" onsubmit="return confirm('{{ $member->is_blocked ? 'Buka blokir' : 'Blokir' }} member ini?')" style="margin:0;">
                            @csrf @method('PUT')
                            <button type="submit" class="{{ $member->is_blocked ? 'btn-edit' : 'btn-delete' }}">
                                <i class="fa-solid {{ $member->is_blocked ? 'fa-lock-open' : 'fa-ban' }}"></i>
                                {{ $member->is_blocked ? 'Buka' : 'Blokir' }}
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="empty-state" style="display: block; text-align: center;">Belum ada member.</td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="pagination">{{ $members->links() }}</div>
</div>
@endsection
