@extends('layouts.dashboard')
@section('title', 'Kelola Admin - Ceritaku')

@section('content')
<div class="dash-header">
    <h1>Kelola Admin</h1>
</div>

{{-- Form Tambah Admin --}}
<div class="dash-card">
    <h3><i class="fa-solid fa-user-plus"></i> Tambah Admin Baru</h3>
    <form action="{{ route('dashboard.admins.store') }}" method="POST" class="dash-form">
        @csrf
        <div class="form-row">
            <div class="form-group">
                <label for="name">Nama</label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" required>
                @error('name') <span class="form-error">{{ $message }}</span> @enderror
            </div>
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" value="{{ old('username') }}" required>
                @error('username') <span class="form-error">{{ $message }}</span> @enderror
            </div>
        </div>
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>
            @error('password') <span class="form-error">{{ $message }}</span> @enderror
        </div>
        <button type="submit" class="btn btn-primary"><i class="fa-solid fa-plus"></i> Tambah Admin</button>
    </form>
</div>

{{-- Daftar Admin --}}
<div class="dash-card" style="overflow-x:auto">
    <h3><i class="fa-solid fa-user-shield"></i> Daftar Admin</h3>
    <table class="dash-table">
        <thead><tr><th>Nama</th><th>Status</th><th>Bergabung</th><th>Aksi</th></tr></thead>
        <tbody>
            @forelse($admins as $admin)
                <tr>
                    <td data-label="Nama"><strong><a href="{{ route('profile.show', $admin->id) }}" style="text-decoration:none; color:inherit;">{{ $admin->name }}</a></strong></td>
                    <td data-label="Status">
                        @if($admin->is_blocked)
                            <span class="status-badge status-blocked">Diblokir</span>
                        @else
                            <span class="status-badge status-active">Aktif</span>
                        @endif
                    </td>
                    <td data-label="Bergabung"><small>{{ $admin->created_at->format('d M Y') }}</small></td>
                    <td data-label="Aksi">
                        <div class="actions">
                            <form action="{{ route('dashboard.users.block', $admin->id) }}" method="POST" style="margin:0;">
                                @csrf @method('PUT')
                                <button type="submit" class="{{ $admin->is_blocked ? 'btn-edit' : 'btn-delete' }}">
                                    <i class="fa-solid {{ $admin->is_blocked ? 'fa-lock-open' : 'fa-ban' }}"></i> {{ $admin->is_blocked ? 'Buka' : 'Blokir' }}
                                </button>
                            </form>
                            <form action="{{ route('dashboard.admins.destroy', $admin->id) }}" method="POST" onsubmit="return confirm('Hapus admin ini?')" style="margin:0;">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-delete"><i class="fa-solid fa-trash"></i> Hapus</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="4" class="empty-state" style="display: block; text-align: center;">Belum ada admin.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
