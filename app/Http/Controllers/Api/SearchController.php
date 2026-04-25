<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Character;
use App\Models\Volume;
use App\Services\ComicVineService;

class SearchController extends Controller
{
    protected ComicVineService $comicVine;

    public function __construct(ComicVineService $comicVine)
    {
        $this->comicVine = $comicVine;
    }

    // Search local DB
    public function search()
    {
        $q = request('q');

        return response()->json([
            'characters' => Character::where('name', 'like', "%$q%")->get(),
            'volumes'    => Volume::where('name', 'like', "%$q%")->get(),
        ]);
    }

    // Search Comic Vine directly
    public function searchComicVine()
    {
        $q = request('q');

        if (!$q) {
            return response()->json(['results' => []]);
        }

        $response = $this->comicVine->search($q);

        return response()->json([
            'results' => $response['results'] ?? [],
        ]);
    }
}