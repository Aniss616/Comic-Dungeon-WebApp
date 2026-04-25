<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Character;
use App\Services\CharacterService;

class CharacterController extends Controller
{
    public function index()
    {
        return Character::paginate(20);
    }

    public function show($id)
    {
        return Character::with('issues.volume')->findOrFail($id);
    }

    public function random(CharacterService $service)
    {
    $character = $service->random();

    if (!$character) {
        return response()->json([
            'message' => 'No characters found'
        ], 404);
    }

    return response()->json($character);
    }
}
