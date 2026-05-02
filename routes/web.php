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

// Protected routes
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/profile',   [ProfileController::class, 'index'])->name('profile');
});