<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ComicController;
use App\Http\Controllers\Api\IssueController;
use App\Http\Controllers\Api\CharacterController;
use App\Http\Controllers\Api\AuthorController;
use App\Http\Controllers\Api\ArtistController;
use App\Http\Controllers\Api\PublisherController;
use App\Http\Controllers\Api\ReadingPathController;
use App\Http\Controllers\Api\AuthController;

// ─── Public routes ───────────────────────────────

// Auth
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login',    [AuthController::class, 'login']);

// Comics
Route::get('/comics',                    [ComicController::class, 'index']);
Route::get('/comics/search',             [ComicController::class, 'search']);
Route::get('/comics/{id}',               [ComicController::class, 'show']);
Route::get('/comics/fetch/{comicVineId}',[ComicController::class, 'fetchFromApi']);

// Issues
Route::get('/issues',                     [IssueController::class, 'index']);
Route::get('/issues/{id}',                [IssueController::class, 'show']);
Route::get('/issues/fetch/{comicVineId}', [IssueController::class, 'fetchFromApi']);

// Characters
Route::get('/characters',                     [CharacterController::class, 'index']);
Route::get('/characters/random',              [CharacterController::class, 'random']);
Route::get('/characters/{id}',                [CharacterController::class, 'show']);
Route::get('/characters/fetch/{comicVineId}', [CharacterController::class, 'fetchFromApi']);

// Publishers
Route::get('/publishers',                     [PublisherController::class, 'index']);
Route::get('/publishers/{id}',                [PublisherController::class, 'show']);
Route::get('/publishers/fetch/{comicVineId}', [PublisherController::class, 'fetchFromApi']);

// Authors
Route::get('/authors',     [AuthorController::class, 'index']);
Route::get('/authors/{id}',[AuthorController::class, 'show']);

// Artists
Route::get('/artists',      [ArtistController::class, 'index']);
Route::get('/artists/{id}', [ArtistController::class, 'show']);

// Reading Paths
Route::get('/reading-paths',      [ReadingPathController::class, 'index']);
Route::get('/reading-paths/{id}', [ReadingPathController::class, 'show']);

// ─── Protected routes (require login) ────────────
Route::middleware('auth:sanctum')->group(function () {

    // Auth
    Route::post('/logout', [AuthController::class, 'logout']);

    // Reading Paths (create)
    Route::post('/reading-paths', [ReadingPathController::class, 'store']);

});