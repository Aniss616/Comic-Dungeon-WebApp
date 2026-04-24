<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Character;
use App\Services\ComicVineService;

class CharacterController extends Controller
{
    protected $comicVine;

    public function __construct(ComicVineService $comicVine)
    {
        $this->comicVine = $comicVine;
    }

    // GET /api/characters
    public function index()
    {
        $characters = Character::all();
        return response()->json($characters);
    }

    // GET /api/characters/{id}
    public function show($id)
    {
        $character = Character::with('issues')->find($id);

        if (!$character) {
            return response()->json(['message' => 'Character not found'], 404);
        }

        return response()->json($character);
    }

    // GET /api/characters/random
    public function random()
    {
        $character = Character::inRandomOrder()->first();

        if (!$character) {
            return response()->json(['message' => 'No characters found'], 404);
        }

        return response()->json($character);
    }

    // Fetch from Comic Vine and store locally
    // GET /api/characters/fetch/{comicVineId}
    public function fetchFromApi($comicVineId)
    {
        // Check if already exists locally
        $existing = Character::where('comic_vine_id', $comicVineId)->first();
        if ($existing) {
            return response()->json($existing);
        }

        // Fetch from Comic Vine
        $data = $this->comicVine->getCharacter($comicVineId);

        if (!$data || $data['status_code'] !== 1) {
            return response()->json(['message' => 'Character not found on Comic Vine'], 404);
        }

        $result = $data['results'];

        // Store locally
        $character = Character::create([
            'comic_vine_id' => $result['id'],
            'name'          => $result['name'],
            'alias'         => $result['aliases'] ?? null,
            'deck'          => $result['deck'] ?? null,
            'avatar_url'    => $result['image']['original_url'] ?? null,
        ]);

        return response()->json($character);
    }
}