<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Comic;
use App\Services\ComicVineService;

class ComicController extends Controller
{
    protected $comicVine;

    public function __construct(ComicVineService $comicVine)
    {
        $this->comicVine = $comicVine;
    }

    // GET /api/comics
    public function index()
    {
        $comics = Comic::with('publishers')->get();
        return response()->json($comics);
    }

    // GET /api/comics/{id}
    public function show($id)
    {
        $comic = Comic::with(['publishers', 'issues'])->find($id);

        if (!$comic) {
            return response()->json(['message' => 'Comic not found'], 404);
        }

        return response()->json($comic);
    }

    // Fetch from Comic Vine and store locally
    // GET /api/comics/fetch/{comicVineId}
    public function fetchFromApi($comicVineId)
    {
        // Check if already exists locally
        $existing = Comic::where('comic_vine_id', $comicVineId)->first();
        if ($existing) {
            return response()->json($existing);
        }

        // Fetch from Comic Vine
        $data = $this->comicVine->getComic($comicVineId);

        if (!$data || $data['status_code'] !== 1) {
            return response()->json(['message' => 'Comic not found on Comic Vine'], 404);
        }

        $result = $data['results'];

        // Store locally
        $comic = Comic::create([
            'comic_vine_id' => $result['id'],
            'title'         => $result['name'],
            'description'   => $result['description'] ?? null,
            'cover_image'   => $result['image']['original_url'] ?? null,
            'start_year'    => $result['start_year'] ?? null,
        ]);

        return response()->json($comic);
    }

    // Search comics on Comic Vine
    // GET /api/comics/search?q=batman
    public function search()
    {
        $query = request('q');

        if (!$query) {
            return response()->json(['message' => 'Search query required'], 400);
        }

        $data = $this->comicVine->search($query);

        return response()->json($data['results'] ?? []);
    }
}