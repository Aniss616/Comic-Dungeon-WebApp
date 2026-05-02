<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\CharacterController;
use App\Http\Controllers\Web\VolumeController;
use App\Http\Controllers\Web\IssueController;
use App\Http\Controllers\Web\SearchController;

Route::get('/', fn() => redirect()->route('characters.index'));

// Auth routes
Route::get('/login',   [AuthController::class, 'showLogin'])->name('login');
Route::post('/login',  [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Public routes
Route::get('/characters',      [CharacterController::class, 'index'])->name('characters.index');
Route::get('/characters/{id}', [CharacterController::class, 'show'])->name('characters.show');
Route::get('/volumes',         [VolumeController::class, 'index'])->name('volumes.index');
Route::get('/volumes/{id}',    [VolumeController::class, 'show'])->name('volumes.show');
Route::get('/issues/{id}',     [IssueController::class, 'show'])->name('issues.show');
Route::get('/search',          [SearchController::class, 'index'])->name('search');

// Protected routes
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
});