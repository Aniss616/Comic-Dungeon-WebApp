<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Character;

class RandomController extends Controller
{
    public function randomCharacter()
{
    $character = Character::with('issues.volume')
        ->inRandomOrder()
        ->first();

    if (!$character) {
        return response()->json([
            'message' => 'No characters found'
        ], 404);
    }

    $issues = $character->issues;

    if ($issues->isEmpty()) {
        return response()->json([
            'character' => $character,
            'message' => 'No issues linked yet (import needed)',
            'first_appearance' => null,
            'reading_path' => [],
            'best_starting_issue' => null,
        ]);
    }

    $sorted = $issues->sortBy([
        ['volume_id', 'asc'],
        ['issue_number', 'asc'],
    ])->values();

    return response()->json([
        'character' => $character,
        'first_appearance' => $sorted->first(),
        'reading_path' => $sorted,
        'best_starting_issue' => $sorted->first(),
    ]);
}
}