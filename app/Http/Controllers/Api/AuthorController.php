<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Author;

class AuthorController extends Controller
{
    // GET /api/authors
    public function index()
    {
        $authors = Author::all();
        return response()->json($authors);
    }

    // GET /api/authors/{id}
    public function show($id)
    {
        $author = Author::with('issues')->find($id);

        if (!$author) {
            return response()->json(['message' => 'Author not found'], 404);
        }

        return response()->json($author);
    }
}