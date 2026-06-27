<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\CharacterController;
use App\Http\Controllers\Web\VolumeController;
use App\Http\Controllers\Web\IssueController;
use App\Http\Controllers\Web\HomeController;
use App\Http\Controllers\Web\ExploreController;
use App\Http\Controllers\Web\ProfileController;
use App\Http\Controllers\Web\UserActionController;
use App\Http\Controllers\Web\TeamController;
use App\Http\Controllers\Web\LocationController;
use App\Http\Controllers\Web\SettingsController;

// Root
Route::get('/', [HomeController::class, 'index'])->name('home');

// Auth routes
Route::get('/login',   [AuthController::class, 'showLogin'])->name('login');
Route::post('/login',  [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/register',  [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');

// Public routes
Route::get('/explore',         [ExploreController::class, 'index'])->name('explore');
Route::get('/characters/{id}', [CharacterController::class, 'show'])->name('characters.show');
Route::get('/volumes/{id}',    [VolumeController::class, 'show'])->name('volumes.show');
Route::get('/issues/{id}',     [IssueController::class, 'show'])->name('issues.show');
Route::get('/random-character', [App\Http\Controllers\Web\RandomController::class, 'randomCharacter'])->name('random.character');
Route::get('/story-arcs/{storyArc}', [App\Http\Controllers\Web\StoryArcController::class, 'show'])->name('story-arcs.show');
Route::get('/teams/{team}', [TeamController::class, 'show'])->name('teams.show');
Route::get('/locations/{location}', [LocationController::class, 'show'])->name('locations.show');

// Admin only
Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
});

// Authenticated users
Route::middleware('auth')->group(function () {
    Route::get('/profile',                        [ProfileController::class, 'index'])->name('profile');
    Route::get('/profile/stats', [ProfileController::class, 'stats'])->name('profile.stats');
    Route::post('/profile/wishlist/{issue}',      [ProfileController::class, 'toggleWishlist'])->name('profile.wishlist.toggle');
    Route::post('/profile/lists',                 [ProfileController::class, 'storeList'])->name('profile.lists.store');
    Route::post('/issues/{id}/read',              [UserActionController::class, 'toggleRead'])->name('issues.toggleRead');
    Route::post('/issues/{id}/favourite',         [UserActionController::class, 'toggleFavouriteIssue'])->name('issues.toggleFavourite');
    Route::post('/characters/{id}/favourite',     [UserActionController::class, 'toggleFavouriteCharacter'])->name('characters.toggleFavourite');
    Route::post('/profile/pin-volume', [ProfileController::class, 'pinVolume'])->name('profile.pin.volume');
    Route::get('/settings',                  [SettingsController::class, 'index'])->name('settings');
    Route::patch('/settings',                [SettingsController::class, 'update'])->name('settings.update');
    Route::get('/settings/volumes/search',   [SettingsController::class, 'searchVolumes'])->name('settings.volumes.search');
    Route::post('/settings/volumes/pin',     [SettingsController::class, 'pinVolume'])->name('settings.volumes.pin');
    Route::post('/settings/volumes/unpin',   [SettingsController::class, 'unpinVolume'])->name('settings.volumes.unpin');
    Route::get('/profile/lists/{list}', [ProfileController::class, 'showList'])->name('profile.lists.show');
    Route::post('/profile/lists/{list}/issues', [ProfileController::class, 'addIssueToList'])->name('profile.lists.issues.add');
    Route::delete('/profile/lists/{list}/issues/{issue}', [ProfileController::class, 'removeIssueFromList'])->name('profile.lists.issues.remove');
});