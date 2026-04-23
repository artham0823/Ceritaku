@extends('layouts.dashboard')
@section('title', 'Kelola Navbar - Ceritaku')

@section('content')
<div class="dash-header">
    <h1>Kelola Navbar</h1>
</div>

{{-- Form Tambah --}}
<div class="dash-card">
    <h3><i class="fa-solid fa-plus-circle"></i> Tambah Navbar Baru</h3>
    <form action="{{ route('dashboard.navbar.store') }}" method="POST" class="dash-form">
        @csrf
        <div class="form-row">
            <div class="form-group">
                <label for="label">Label</label>
                <input type="text" id="label" name="label" placeholder="Contoh: Beranda" required>
            </div>
            <div class="form-group">
                <label for="url">URL</label>
                <input type="text" id="url" name="url" placeholder="Contoh: / atau /explore" required>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label for="icon">Icon (FontAwesome)</label>
                <input type="text" id="icon" name="icon" placeholder="Contoh: fa-solid fa-house">
            </div>
            <div class="form-group">
                <label for="sort_order">Urutan</label>
                <input type="number" id="sort_order" name="sort_order" value="{{ ($navbarItems->max('sort_order') ?? 0) + 1 }}" min="1">
            </div>
        </div>
        <button type="submit" class="btn btn-primary"><i class="fa-solid fa-plus"></i> Tambah</button>
    </form>
</div>

{{-- Daftar Navbar --}}
<div class="dash-card" style="overflow-x:auto">
    <h3><i class="fa-solid fa-bars"></i> Daftar Navbar</h3>
    <table class="dash-table">
        <thead><tr><th>Urutan</th><th>Label</th><th>URL</th><th>Icon</th><th>Status</th><th>Aksi</th></tr></thead>
        <tbody>
            @foreach($navbarItems as $item)
                <tr>
                    <td data-label="Urutan">{{ $item->sort_order }}</td>
                    <td data-label="Label"><strong>{{ $item->label }}</strong></td>
                    <td data-label="URL"><code>{{ $item->url }}</code></td>
                    <td data-label="Icon"><i class="{{ $item->icon }}"></i> {{ $item->icon }}</td>
                    <td data-label="Status"><span class="status-badge {{ $item->is_active ? 'status-active' : 'status-blocked' }}">{{ $item->is_active ? 'Aktif' : 'Nonaktif' }}</span></td>
                    <td data-label="Aksi">
                        <div class="actions">
                            <form action="{{ route('dashboard.navbar.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Hapus navbar ini?')" style="margin:0;">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-delete"><i class="fa-solid fa-trash"></i> Hapus</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
