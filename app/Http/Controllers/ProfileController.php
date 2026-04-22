<?php

namespace App\Http\Controllers;

/**
 * =====================================================
 * CONTROLLER: ProfileController (Profil & Kelola Akun)
 * =====================================================
 * Mengelola:
 * - Tampilkan profil publik user (termasuk sosmed)
 * - Edit profil sendiri (semua role)
 * - Tambah/hapus link sosial media di profil
 * - Kelola admin (hanya author)
 * - Block/unblock user
 * =====================================================
 */

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\SocialLink;
use App\Models\Notification;

class ProfileController extends Controller
{
    /**
     * Tampilkan profil publik user.
     * Menampilkan: info user, sosmed, cerita (jika author), komentar terakhir.
     */
    public function show($id)
    {
        // Ambil data user beserta link sosmed-nya
        $user = User::with('socialLinks')->findOrFail($id);
        
        // Ambil 5 komentar terbaru dari user ini
        $recentComments = $user->comments()->with('chapter.story')->orderByDesc('created_at')->limit(5)->get();

        // Ambil cerita yang ditulis user ini (jika author)
        $authoredStories = $user->stories()->withCount('chapters')->orderByDesc('created_at')->get();
        
        return view('profile.show', compact('user', 'recentComments', 'authoredStories'));
    }

    /**
     * Tampilkan halaman edit profil sendiri.
     * Memuat data user yang sedang login beserta sosmed-nya.
     */
    public function edit()
    {
        $user = auth()->user();
        $socialLinks = $user->socialLinks()->orderBy('sort_order')->get();
        return view('dashboard.profile', compact('user', 'socialLinks'));
    }

    /**
     * Proses update profil user.
     * Yang bisa diubah: nama, bio, avatar, title (author only).
     */
    public function update(Request $request)
    {
        $user = auth()->user();

        $rules = [
            'name' => 'required|string|max:100',
            'bio' => 'nullable|string|max:500',
            'avatar' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ];

        // Author bisa ubah title/gelar
        if ($user->isAuthor()) {
            $rules['title'] = 'nullable|string|max:100';
        }

        $request->validate($rules, [
            'name.required' => 'Nama wajib diisi.',
            'avatar.image' => 'File harus berupa gambar.',
            'avatar.max' => 'Ukuran foto maksimal 2MB.',
        ]);

        // Upload avatar baru jika ada
        if ($request->hasFile('avatar')) {
            $file = $request->file('avatar');
            $filename = 'avatar_' . $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('img/avatars'), $filename);
            $user->avatar = 'img/avatars/' . $filename;
        }

        $user->name = $request->name;
        $user->bio = $request->bio;

        // Update title hanya jika user adalah author
        if ($user->isAuthor() && $request->has('title')) {
            $user->title = $request->title;
        }
        $user->save();

        return back()->with('success', 'Profil berhasil diperbarui!');
    }

    /**
     * Tambah link sosial media / game ID ke profil.
     * Batas: Member max 10, Admin & Author unlimited.
     */
    public function storeSocialLink(Request $request)
    {
        $user = auth()->user();

        // Validasi input
        $request->validate([
            'icon' => 'required|string|max:100',
            'label' => 'required|string|max:100',
            'value' => 'required|string|max:255',
        ], [
            'icon.required' => 'Icon wajib dipilih.',
            'label.required' => 'Label wajib diisi.',
            'value.required' => 'Link / ID wajib diisi.',
        ]);

        // Cek apakah sudah mencapai batas
        $currentCount = $user->socialLinks()->count();
        $limit = $user->getSocialLinksLimit();

        if ($currentCount >= $limit) {
            return back()->with('error', "Anda sudah mencapai batas maksimal ($limit) link sosial media.");
        }

        // Simpan link sosmed baru
        SocialLink::create([
            'user_id' => $user->id,
            'icon' => $request->icon,
            'label' => $request->label,
            'value' => $request->value,
            'sort_order' => $currentCount, // Urutan otomatis di akhir
        ]);

        return back()->with('success', 'Link sosial media berhasil ditambahkan!');
    }

    /**
     * Hapus link sosial media dari profil.
     * User hanya bisa menghapus miliknya sendiri.
     */
    public function destroySocialLink($id)
    {
        $link = SocialLink::where('user_id', auth()->id())->findOrFail($id);
        $link->delete();

        return back()->with('success', 'Link sosial media berhasil dihapus.');
    }

    // ==========================================
    // Kelola Admin (Hanya Author)
    // ==========================================

    /** Tampilkan daftar admin */
    public function manageAdmins()
    {
        $admins = User::where('role', 'admin')->orderBy('name')->get();
        return view('dashboard.admins', compact('admins'));
    }

    /** Buat admin baru */
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

        // Cegah membuat akun dengan username "artham"
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

    /** Update data admin */
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

    /** Hapus admin */
    public function destroyAdmin($id)
    {
        $admin = User::where('role', 'admin')->findOrFail($id);
        $name = $admin->name;
        $admin->delete();

        return back()->with('success', "Admin '{$name}' berhasil dihapus!");
    }

    // ==========================================
    // Block / Unblock User
    // ==========================================

    /**
     * Toggle blokir user.
     * Author tidak bisa diblokir.
     * Admin hanya bisa block member.
     */
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

        // Toggle status blokir
        $targetUser->is_blocked = !$targetUser->is_blocked;
        $targetUser->save();

        $action = $targetUser->is_blocked ? 'diblokir' : 'dibuka blokirnya';
        return back()->with('success', "Akun '{$targetUser->name}' berhasil {$action}.");
    }
}
