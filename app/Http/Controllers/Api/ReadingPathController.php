<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ReadingPath;

class ReadingPathController extends Controller
{
    // GET /api/reading-paths
    public function index()
    {
        $paths = ReadingPath::all();
        return response()->json($paths);
    }

    // GET /api/reading-paths/{id}
    public function show($id)
    {
        $path = ReadingPath::with([
            'issues.comic',
            'issues.characters'
        ])->find($id);

        if (!$path) {
            return response()->json(['message' => 'Reading path not found'], 404);
        }

        return response()->json($path);
    }

    // POST /api/reading-paths
    public function store()
    {
        $path = ReadingPath::create([
            'title'       => request('title'),
            'mood'        => request('mood'),
            'description' => request('description'),
            'difficulty'  => request('difficulty'),
        ]);

        return response()->json($path, 201);
    }
}