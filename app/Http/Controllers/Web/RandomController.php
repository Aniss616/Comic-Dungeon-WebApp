<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Character;

class RandomController extends Controller
{
    public function randomCharacter()
    {
        $character = Character::with('issues.volume')
            ->whereNotNull('image')
            ->inRandomOrder()
            ->first();

        if (!$character) {
            return response()->json(['error' => 'No characters found.'], 404);
        }

        $sorted = $character->issues->sortBy([
            ['volume_id', 'asc'],
            ['issue_number', 'asc'],
        ])->values();

        $firstAppearance   = $sorted->first();
        $bestStartingIssue = $sorted->first();

        return response()->json([
            'character' => [
                'id'        => $character->id,
                'name'      => $character->name,
                'real_name' => $character->real_name,
                'aliases'   => $character->aliases,
                'image'     => $character->image,
                'publisher' => $character->publisher,
                'origin'    => $character->origin,
                'gender'    => $character->gender_label,
                'powers'    => $character->powers,
            ],
            'first_appearance' => $firstAppearance ? [
                'id'           => $firstAppearance->id,
                'name'         => $firstAppearance->name,
                'issue_number' => $firstAppearance->issue_number,
                'image'        => $firstAppearance->image,
                'cover_date'   => $firstAppearance->cover_date,
                'volume_name'  => $firstAppearance->volume->name ?? null,
            ] : null,
            'best_start' => $bestStartingIssue ? [
                'id'           => $bestStartingIssue->id,
                'name'         => $bestStartingIssue->name,
                'issue_number' => $bestStartingIssue->issue_number,
                'image'        => $bestStartingIssue->image,
                'volume_name'  => $bestStartingIssue->volume->name ?? null,
            ] : null,
        ]);
    }
}