<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Publisher;
use App\Services\ComicVineService;

class PublisherController extends Controller
{
    protected $comicVine;

    public function __construct(ComicVineService $comicVine)
    {
        $this->comicVine = $comicVine;
    }

    // GET /api/publishers
    public function index()
    {
        $publishers = Publisher::all();
        return response()->json($publishers);
    }

    // GET /api/publishers/{id}
    public function show($id)
    {
        $publisher = Publisher::with('comics')->find($id);

        if (!$publisher) {
            return response()->json(['message' => 'Publisher not found'], 404);
        }

        return response()->json($publisher);
    }

    // Fetch from Comic Vine and store locally
    // GET /api/publishers/fetch/{comicVineId}
    public function fetchFromApi($comicVineId)
    {
        $existing = Publisher::where('comic_vine_id', $comicVineId)->first();
        if ($existing) {
            return response()->json($existing);
        }

        $data = $this->comicVine->getPublisher($comicVineId);

        if (!$data || $data['status_code'] !== 1) {
            return response()->json(['message' => 'Publisher not found on Comic Vine'], 404);
        }

        $result = $data['results'];

        $publisher = Publisher::create([
            'comic_vine_id' => $result['id'],
            'name'          => $result['name'],
            'description'   => $result['deck'] ?? null,
            'logo_url'      => $result['image']['original_url'] ?? null,
        ]);

        return response()->json($publisher);
    }
}