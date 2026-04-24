<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Artist;

class ArtistController extends Controller
{
    // GET /api/artists
    public function index()
    {
        $artists = Artist::all();
        return response()->json($artists);
    }

    // GET /api/artists/{id}
    public function show($id)
    {
        $artist = Artist::with('issues')->find($id);

        if (!$artist) {
            return response()->json(['message' => 'Artist not found'], 404);
        }

        return response()->json($artist);
    }
}