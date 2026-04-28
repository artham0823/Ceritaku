@extends('layouts.dashboard')
@section('title', 'Notifikasi - Ceritaku')

@section('content')
<div class="dash-header">
    <h1>Notifikasi</h1>
</div>

<div class="dash-card">
    @forelse($notifications as $notif)
        <div style="padding:1rem;border-bottom:1px solid var(--border-color);display:flex;align-items:flex-start;gap:1rem">
            <div style="width:40px;height:40px;border-radius:50%;background:var(--primary-glow);display:flex;align-items:center;justify-content:center;flex-shrink:0">
                <i class="fa-solid fa-bell" style="color:var(--primary-color)"></i>
            </div>
            <div style="flex:1">
                <p style="font-size:0.95rem">{{ $notif->message }}</p>
                <small style="color:var(--text-muted)">
                    {{ $notif->actor_username ? 'Oleh: ' . $notif->actor_username . ' — ' : '' }}
                    {{ $notif->created_at ? $notif->created_at->diffForHumans() : '' }}
                </small>
            </div>
        </div>
    @empty
        <div class="empty-state">
            <i class="fa-solid fa-bell-slash"></i>
            <p>Belum ada notifikasi.</p>
        </div>
    @endforelse
    <div style="margin-top: 1.5rem;">
        {{ $notifications->links('vendor.pagination.custom') }}
    </div>
</div>
@endsection
