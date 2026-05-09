<?php

namespace App\Services;

use App\Models\User;
use App\Models\Issue;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class RecommendationService
{
    /**
     * Generate a ranked list of recommended issues for a given user.
     *
     * Two signals are used:
     *   1. Favourite Issues  → more unread issues from the same volume(s)
     *   2. Favourite Characters → issues containing those characters,
     *      ranked by how many favourited characters appear in each issue.
     *
     * Both signals contribute a "score" per issue. The lists are merged,
     * de-duplicated, and sorted by descending score before being returned.
     *
     * @param  User  $user
     * @param  int   $limit  Maximum number of recommendations to return
     * @return Collection<Issue>  Issues with an appended `recommendation_score` attribute
     */
    public function getRecommendations(User $user, int $limit = 20): Collection
    {
        $readIssueIds      = $user->reads()->pluck('issues.id')->toArray();
        $favouriteIssueIds = $user->favourites()->pluck('issues.id')->toArray();

        $scores = []; // issue_id => int score

        // ----------------------------------------------------------------
        // Signal 1: Favourite Issues → more issues from the same volumes
        // ----------------------------------------------------------------
        if (!empty($favouriteIssueIds)) {
            // Find all volume IDs that the user has favourited an issue from
            $volumeIds = Issue::whereIn('id', $favouriteIssueIds)
                ->pluck('volume_id')
                ->unique()
                ->toArray();

            // Fetch sibling issues in those volumes that haven't been read yet
            $volumeIssues = Issue::whereIn('volume_id', $volumeIds)
                ->whereNotIn('id', $readIssueIds)
                ->whereNotIn('id', $favouriteIssueIds) // already seen
                ->get();

            foreach ($volumeIssues as $issue) {
                // Base score of 1 per signal; multiple favourite issues from
                // the same volume naturally stack since each contributes once.
                $scores[$issue->id] = ($scores[$issue->id] ?? 0) + 1;
            }
        }

        // ----------------------------------------------------------------
        // Signal 2: Favourite Characters → issues containing those characters
        // ----------------------------------------------------------------
        $favouriteCharacterIds = $user->favouriteCharacters()->pluck('characters.id')->toArray();

        if (!empty($favouriteCharacterIds)) {
            // For each issue that contains at least one favourited character,
            // count how many of the user's favourited characters appear in it.
            // We do this efficiently with a single GROUP BY query.
            $rows = \DB::table('issue_characters')
                ->select('issue_id', \DB::raw('COUNT(*) as match_count'))
                ->whereIn('character_id', $favouriteCharacterIds)
                ->whereNotIn('issue_id', $readIssueIds)
                ->groupBy('issue_id')
                ->get();

            foreach ($rows as $row) {
                // Each matched character adds 2 points so character-driven
                // recommendations outrank generic same-volume suggestions
                // when a character appears repeatedly in the user's favourites.
                $scores[$row->issue_id] = ($scores[$row->issue_id] ?? 0) + ($row->match_count * 2);
            }
        }

        if (empty($scores)) {
            return collect();
        }

        // Sort by score descending, keep top $limit candidates
        arsort($scores);
        $topIds    = array_keys(array_slice($scores, 0, $limit, true));
        $scoreMap  = $scores;

        // Fetch the actual Issue models with their volume (for display)
        $issues = Issue::whereIn('id', $topIds)
            ->with(['volume.publisher'])
            ->get()
            ->map(function (Issue $issue) use ($scoreMap) {
                $issue->recommendation_score = $scoreMap[$issue->id] ?? 0;
                return $issue;
            })
            ->sortByDesc('recommendation_score')
            ->values();

        return $issues;
    }
}