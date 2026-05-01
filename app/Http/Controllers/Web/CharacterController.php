<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Character;

class CharacterController extends Controller
{
    public function index()
    {
        $characters = Character::orderBy('name')
            ->paginate(24);

        return view('characters.index', compact('characters'));
    }

    public function show(int $id)
    {
        $character = Character::with('issues.volume')
            ->findOrFail($id);

        $issues = $character->issues->sortBy([
            ['volume_id', 'asc'],
            ['issue_number', 'asc'],
        ])->values();

        $firstAppearance = $character->issues
            ->whereNotNull('cover_date')
            ->sortBy('cover_date')
            ->first() ?? $issues->first();

        $bestStart = $issues->firstWhere('issue_number', 1) ?? $issues->first();

        return view('characters.show', compact('character', 'issues', 'firstAppearance', 'bestStart'));
    }
}