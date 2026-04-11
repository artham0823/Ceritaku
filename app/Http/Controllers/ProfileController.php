<?php

namespace App\Http\Controllers;

/**
 * =====================================================
 * CONTROLLER: ProfileController (Profil & Kelola Akun)
 * =====================================================
 * Mengelola:
 * - Edit profil sendiri (semua role)
 * - Kelola admin (hanya author)
 * - Block/unblock user
 * =====================================================
 */

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Notification;

class ProfileController extends Controller
{
    /** Detail profil publik */
    public function show($id)
    {
        $user = User::findOrFail($id);
        
        $recentComments = $user->comments()->with('chapter.story')->orderByDesc('created_at')->limit(5)->get();
        $authoredStories = $user->stories()->withCount('chapters')->orderByDesc('created_at')->get();
        
        return view('profile.show', compact('user', 'recentComments', 'authoredStories'));
    }

    /** Form edit profil */
    public function edit()
    {
        return view('dashboard.profile', ['user' => auth()->user()]);
    }

    /** Update profil */
    public function update(Request $request)
    {
        $user = auth()->user();

        $rules = [
            'name' => 'required|string|max:100',
            'bio' => 'nullable|string|max:500',
            'avatar' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ];

        // Author bisa ubah title
        if ($user->isAuthor()) {
            $rules['title'] = 'nullable|string|max:100';
        }

        $request->validate($rules, [
            'name.required' => 'Nama wajib diisi.',
            'avatar.image' => 'File harus berupa gambar.',
            'avatar.max' => 'Ukuran foto maksimal 2MB.',
        ]);

        if ($request->hasFile('avatar')) {
            $file = $request->file('avatar');
            $filename = 'avatar_' . $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('img/avatars'), $filename);
            $user->avatar = 'img/avatars/' . $filename;
        }

        $user->name = $request->name;
        $user->bio = $request->bio;
        if ($user->isAuthor() && $request->has('title')) {
            $user->title = $request->title;
        }
        $user->save();

        return back()->with('success', 'Profil berhasil diperbarui!');
    }

    /** Daftar admin (hanya author) */
    public function manageAdmins()
    {
        $admins = User::where('role', 'admin')->orderBy('name')->get();
        return view('dashboard.admins', compact('admins'));
    }

    /** Buat admin baru (hanya author) */
    public function storeAdmin(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'username' => 'required|string|max:50|unique:users,username|alpha_dash',
            'password' => 'required|string|min:6',
        ], [
            'name.required' => 'Nama admin wajib diisi.',
            'username.required' => 'Username wajib diisi.',
            'username.unique' => 'Username sudah digunakan.',
            'password.required' => 'Password wajib diisi.',
        ]);

        if (strtolower($request->username) === 'artham') {
            return back()->withErrors(['username' => 'Username ini tidak boleh digunakan.'])->withInput();
        }

        User::create([
            'name' => $request->name,
            'username' => $request->username,
            'password' => $request->password,
            'role' => 'admin',
        ]);

        return back()->with('success', "Admin '{$request->name}' berhasil dibuat!");
    }

    /** Update admin (hanya author) */
    public function updateAdmin(Request $request, $id)
    {
        $admin = User::where('role', 'admin')->findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:100',
            'username' => 'required|string|max:50|alpha_dash|unique:users,username,' . $id,
            'password' => 'nullable|string|min:6',
        ]);

        $admin->name = $request->name;
        $admin->username = $request->username;
        if ($request->filled('password')) {
            $admin->password = $request->password;
        }
        $admin->save();

        return back()->with('success', "Admin '{$admin->name}' berhasil diperbarui!");
    }

    /** Hapus admin (hanya author) */
    public function destroyAdmin($id)
    {
        $admin = User::where('role', 'admin')->findOrFail($id);
        $name = $admin->name;
        $admin->delete();

        return back()->with('success', "Admin '{$name}' berhasil dihapus!");
    }

    /** Block/unblock user */
    public function blockUser($id)
    {
        $targetUser = User::findOrFail($id);
        $currentUser = auth()->user();

        // Author tidak bisa diblokir
        if ($targetUser->isAuthor()) {
            return back()->with('error', 'Akun author tidak bisa diblokir.');
        }

        // Admin hanya bisa block member
        if ($currentUser->isAdmin() && !$targetUser->isMember()) {
            return back()->with('error', 'Anda hanya bisa memblokir akun member.');
        }

        $targetUser->is_blocked = !$targetUser->is_blocked;
        $targetUser->save();

        $action = $targetUser->is_blocked ? 'diblokir' : 'dibuka blokirnya';
        return back()->with('success', "Akun '{$targetUser->name}' berhasil {$action}.");
    }
}
