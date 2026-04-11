<?php

namespace App\Http\Controllers;

/**
 * =====================================================
 * CONTROLLER: NavbarController (Kelola Navigasi)
 * =====================================================
 * Hanya author yang bisa mengelola navbar.
 * Author bisa menambah, mengubah, dan menghapus item navigasi.
 * =====================================================
 */

use Illuminate\Http\Request;
use App\Models\NavbarItem;

class NavbarController extends Controller
{
    /** Daftar navbar items */
    public function index()
    {
        $navbarItems = NavbarItem::orderBy('sort_order')->get();
        return view('dashboard.navbar', compact('navbarItems'));
    }

    /** Simpan navbar baru */
    public function store(Request $request)
    {
        $request->validate([
            'label' => 'required|string|max:100',
            'url' => 'required|string|max:255',
            'icon' => 'nullable|string|max:100',
            'sort_order' => 'nullable|integer',
        ]);

        NavbarItem::create([
            'label' => $request->label,
            'url' => $request->url,
            'icon' => $request->icon,
            'sort_order' => $request->sort_order ?? NavbarItem::max('sort_order') + 1,
        ]);

        return back()->with('success', "Navbar '{$request->label}' berhasil ditambahkan!");
    }

    /** Update navbar */
    public function update(Request $request, $id)
    {
        $item = NavbarItem::findOrFail($id);

        $request->validate([
            'label' => 'required|string|max:100',
            'url' => 'required|string|max:255',
            'icon' => 'nullable|string|max:100',
            'sort_order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);

        $item->update([
            'label' => $request->label,
            'url' => $request->url,
            'icon' => $request->icon,
            'sort_order' => $request->sort_order ?? $item->sort_order,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return back()->with('success', "Navbar '{$item->label}' berhasil diperbarui!");
    }

    /** Hapus navbar */
    public function destroy($id)
    {
        $item = NavbarItem::findOrFail($id);
        $label = $item->label;
        $item->delete();

        return back()->with('success', "Navbar '{$label}' berhasil dihapus!");
    }
}
