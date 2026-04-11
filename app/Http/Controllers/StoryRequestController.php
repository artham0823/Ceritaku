<?php

namespace App\Http\Controllers;

/**
 * =====================================================
 * CONTROLLER: StoryRequestController (Request Cerita)
 * =====================================================
 * Mengelola permintaan cerita dari pengguna.
 * Form terletak di atas footer.
 * =====================================================
 */

use Illuminate\Http\Request;
use App\Models\StoryRequest;
use App\Models\Notification;
use App\Models\User;

class StoryRequestController extends Controller
{
    /** Kirim request cerita baru */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
        ], [
            'title.required' => 'Judul cerita yang diminta wajib diisi.',
        ]);

        StoryRequest::create([
            'user_id' => auth()->id(),
            'title' => $request->title,
            'description' => $request->description,
        ]);

        // Notifikasi ke author dan admin
        $author = User::where('role', 'author')->first();
        if ($author) {
            Notification::createForUser(
                $author,
                'request_cerita',
                auth()->user()->name . " meminta cerita: \"{$request->title}\"",
                auth()->user()->username
            );
        }

        return back()->with('success', 'Request cerita berhasil dikirim! Terima kasih.');
    }

    /** Daftar request (dashboard author/admin) */
    public function index()
    {
        $requests = StoryRequest::with('user')
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('dashboard.requests', compact('requests'));
    }

    /** Update status request */
    public function updateStatus(Request $request, $id)
    {
        $storyRequest = StoryRequest::findOrFail($id);

        $request->validate([
            'status' => 'required|in:approved,rejected',
        ]);

        $storyRequest->update(['status' => $request->status]);

        $statusText = $request->status === 'approved' ? 'disetujui' : 'ditolak';
        return back()->with('success', "Request cerita '{$storyRequest->title}' telah {$statusText}.");
    }
}
