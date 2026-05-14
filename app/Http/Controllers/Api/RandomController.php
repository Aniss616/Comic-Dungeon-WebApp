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
        return response()->json(['message' => 'No characters found'], 404);
    }

    $issues = $character->issues;

    // Build first appearance from the dedicated JSON column
    $firstAppearance = null;
    if ($character->first_appeared_in_issue) {
        $fa = $character->first_appeared_in_issue;
        $cvId = $fa['id'] ?? null;

        // Try to find it in local DB for image + full data
        $localIssue = $cvId
            ? $issues->firstWhere('comic_vine_id', $cvId)
            : null;

        $firstAppearance = $localIssue
            ? $this->formatIssue($localIssue)
            : [
                'id'          => null,
                'comic_vine_id' => $cvId,
                'issue_number' => $fa['issue_number'] ?? null,
                'name'        => $fa['name'] ?? null,
                'volume_name' => $fa['volume']['name'] ?? null,
                'image'       => null,
            ];
    }

    // Best start = lowest issue_number in the earliest volume (by cover_date)
    $bestStart = null;
    if ($issues->isNotEmpty()) {
        $sorted = $issues
            ->sortBy(fn($i) => [
                optional($i->volume)->issues->min('cover_date'),
                $i->issue_number,
            ])
            ->values();

        $bestStart = $this->formatIssue($sorted->first());
    }

    return response()->json([
        'character'        => $character,
        'first_appearance' => $firstAppearance,
        'best_start'       => $bestStart,         // key now matches frontend
    ]);
}

private function formatIssue($issue): array
{
    return [
        'id'           => $issue->id,
        'comic_vine_id'=> $issue->comic_vine_id,
        'issue_number' => $issue->issue_number,
        'name'         => $issue->name,
        'volume_name'  => $issue->volume?->name,
        'image'        => $issue->image,
    ];
}
}