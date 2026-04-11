<?php

/**
 * =====================================================
 * ROUTES: Web Routes
 * =====================================================
 * Semua rute web aplikasi Ceritaku.
 * Dikelompokkan berdasarkan:
 * 1. Rute Publik (tanpa login)
 * 2. Rute Autentikasi (login/register/logout)
 * 3. Rute Authenticated (memerlukan login)
 * 4. Rute Author/Admin (kelola konten)
 * 5. Rute Author Only (kelola admin, navbar)
 * =====================================================
 */

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\StoryController;
use App\Http\Controllers\ChapterController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StoryRequestController;
use App\Http\Controllers\NavbarController;
use App\Http\Controllers\LikeController;

// =============================================
// 1. RUTE PUBLIK (Bisa diakses tanpa login)
// =============================================
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/explore', [HomeController::class, 'explore'])->name('explore');
Route::get('/popular', [HomeController::class, 'popular'])->name('popular');
Route::get('/search', [HomeController::class, 'search'])->name('search');
Route::get('/story/{id}', [StoryController::class, 'show'])->name('story.show');
Route::get('/story/{storyId}/chapter/{chapterId}', [ChapterController::class, 'show'])->name('chapter.show');

// Like (bisa tanpa login — guest pakai IP)
Route::post('/like/{storyId}', [LikeController::class, 'toggleLike'])->name('like.toggle');

// =============================================
// 2. RUTE AUTENTIKASI
// =============================================
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.process');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.process');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// =============================================
// 3. RUTE AUTHENTICATED (Memerlukan login)
// =============================================
Route::middleware('auth')->group(function () {
    // Komentar
    Route::post('/comment', [CommentController::class, 'store'])->name('comment.store');
    Route::delete('/comment/{id}', [CommentController::class, 'destroy'])->name('comment.destroy');

    // Request cerita
    Route::post('/story-request', [StoryRequestController::class, 'store'])->name('story-request.store');

    // Favorit
    Route::post('/favorite/{storyId}', [LikeController::class, 'toggleFavorite'])->name('favorite.toggle');

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/history', [DashboardController::class, 'history'])->name('dashboard.history');
    Route::get('/dashboard/notifications', [DashboardController::class, 'notifications'])->name('dashboard.notifications');

    // Profil
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
});

// =============================================
// 4. RUTE AUTHOR/ADMIN (Kelola konten)
// =============================================
Route::middleware(['auth', 'role:author,admin'])->group(function () {
    // CRUD Cerita
    Route::get('/dashboard/stories', [StoryController::class, 'index'])->name('dashboard.stories.index');
    Route::get('/dashboard/stories/create', [StoryController::class, 'create'])->name('dashboard.stories.create');
    Route::post('/dashboard/stories', [StoryController::class, 'store'])->name('dashboard.stories.store');
    Route::get('/dashboard/stories/{id}/edit', [StoryController::class, 'edit'])->name('dashboard.stories.edit');
    Route::put('/dashboard/stories/{id}', [StoryController::class, 'update'])->name('dashboard.stories.update');
    Route::delete('/dashboard/stories/{id}', [StoryController::class, 'destroy'])->name('dashboard.stories.destroy');

    // CRUD Chapter
    Route::get('/dashboard/stories/{storyId}/chapters/create', [ChapterController::class, 'create'])->name('dashboard.chapters.create');
    Route::post('/dashboard/stories/{storyId}/chapters', [ChapterController::class, 'store'])->name('dashboard.chapters.store');
    Route::get('/dashboard/chapters/{id}/edit', [ChapterController::class, 'edit'])->name('dashboard.chapters.edit');
    Route::put('/dashboard/chapters/{id}', [ChapterController::class, 'update'])->name('dashboard.chapters.update');
    Route::delete('/dashboard/chapters/{id}', [ChapterController::class, 'destroy'])->name('dashboard.chapters.destroy');

    // Komentar (lihat semua)
    Route::get('/dashboard/comments', [DashboardController::class, 'comments'])->name('dashboard.comments');

    // Member (lihat semua)
    Route::get('/dashboard/members', [DashboardController::class, 'members'])->name('dashboard.members');

    // Request cerita
    Route::get('/dashboard/requests', [StoryRequestController::class, 'index'])->name('dashboard.requests');
    Route::put('/dashboard/requests/{id}', [StoryRequestController::class, 'updateStatus'])->name('dashboard.requests.update');

    // Block user (admin bisa block member)
    Route::put('/dashboard/users/{id}/block', [ProfileController::class, 'blockUser'])->name('dashboard.users.block');
});

// =============================================
// 5. RUTE AUTHOR ONLY (Kelola admin, navbar)
// =============================================
Route::middleware(['auth', 'role:author'])->group(function () {
    // Kelola admin
    Route::get('/dashboard/admins', [ProfileController::class, 'manageAdmins'])->name('dashboard.admins');
    Route::post('/dashboard/admins', [ProfileController::class, 'storeAdmin'])->name('dashboard.admins.store');
    Route::put('/dashboard/admins/{id}', [ProfileController::class, 'updateAdmin'])->name('dashboard.admins.update');
    Route::delete('/dashboard/admins/{id}', [ProfileController::class, 'destroyAdmin'])->name('dashboard.admins.destroy');

    // Kelola navbar
    Route::get('/dashboard/navbar', [NavbarController::class, 'index'])->name('dashboard.navbar');
    Route::post('/dashboard/navbar', [NavbarController::class, 'store'])->name('dashboard.navbar.store');
    Route::put('/dashboard/navbar/{id}', [NavbarController::class, 'update'])->name('dashboard.navbar.update');
    Route::delete('/dashboard/navbar/{id}', [NavbarController::class, 'destroy'])->name('dashboard.navbar.destroy');
});
