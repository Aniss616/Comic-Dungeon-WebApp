<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Character;

class RandomController extends Controller
{
    public function randomCharacter()
    {
        $character = Character::with(['issues.volume'])
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
                'message' => 'No issues found for this character'
            ]);
        }

        // ✅ Best logic: sort by volume + issue number
        $sortedIssues = $issues->sortBy([
            ['volume_id', 'asc'],
            ['issue_number', 'asc'],
        ])->values();

        $firstAppearance = $sortedIssues->first();

        
        $readingPath = $sortedIssues;

        
        $bestStart = $firstAppearance;

        return response()->json([
            'character' => $character,
            'first_appearance' => $firstAppearance,
            'reading_path' => $readingPath,
            'best_starting_issue' => $bestStart,
        ]);
    }
}