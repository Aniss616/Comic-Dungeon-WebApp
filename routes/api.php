<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CharacterController;
use App\Http\Controllers\Api\VolumeController;
use App\Http\Controllers\Api\IssueController;
use App\Http\Controllers\Api\PublisherController;
use App\Http\Controllers\Api\SearchController;
use App\Http\Controllers\Api\RandomController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ImportController;

/*
|--------------------------------------------------------------------------
| AUTH
|--------------------------------------------------------------------------
*/
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login',    [AuthController::class, 'login']);

/*
|--------------------------------------------------------------------------
| PUBLIC API (DB-BASED)
|--------------------------------------------------------------------------
*/

// Characters
Route::get('/characters',        [CharacterController::class, 'index']);
Route::get('/characters/random', [RandomController::class, 'randomCharacter']);
Route::get('/characters/{id}',   [CharacterController::class, 'show']);

// Volumes
Route::get('/volumes',      [VolumeController::class, 'index']);
Route::get('/volumes/{id}', [VolumeController::class, 'show']);

// Issues
Route::get('/issues/{id}', [IssueController::class, 'show']);

// Publishers
Route::get('/publishers',      [PublisherController::class, 'index']);
Route::get('/publishers/{id}', [PublisherController::class, 'show']);

// Search
Route::get('/search', [SearchController::class, 'search']);

//people
Route::post('/persons',      [ImportController::class, 'persons']);
Route::post('/persons/{id}', [ImportController::class, 'person']);

/*
|--------------------------------------------------------------------------
| IMPORT ROUTES
|--------------------------------------------------------------------------
*/
Route::prefix('import')->group(function () {
    // Characters
    Route::post('/characters',      [ImportController::class, 'characters']);
    Route::post('/characters/{id}', [ImportController::class, 'character']);

    // Publishers
    Route::post('/publishers/{id}', [ImportController::class, 'publisher']);

    // Volumes
    Route::post('/volumes',      [ImportController::class, 'volumes']);
    Route::post('/volumes/{id}', [ImportController::class, 'volume']);

    // Issues
    Route::post('/volumes/{volumeId}/issues', [ImportController::class, 'issues']);
    Route::post('/issues/{id}',               [ImportController::class, 'issue']);
});

/*
|--------------------------------------------------------------------------
| PROTECTED ROUTES
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
});