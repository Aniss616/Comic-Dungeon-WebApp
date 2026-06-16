<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Issue;
use App\Traits\ParsesDescription;
use App\Models\Team;
use App\Models\Location;

class IssueController extends Controller
{
    use ParsesDescription;

    public function show(int $id)
    {
        $issue = Issue::with(['volume.publisher', 'characters', 'people'])
            ->findOrFail($id);
        $descriptionSections = $issue->description
            ? $this->parseDescription($issue->description)
            : [];

        $linkedTeams = collect($issue->teams ?? [])
            ->filter(fn ($t) => !empty($t['name']))
            ->values()
            ->map(fn ($t) => [
                'name' => $t['name'],
                'team' => isset($t['id']) ? Team::where('comic_vine_id', $t['id'])->first() : null,
            ]);

        $linkedLocations = collect($issue->locations ?? [])
            ->filter(fn ($l) => !empty($l['name']))
            ->values()
            ->map(fn ($l) => [
                'name' => $l['name'],
                'location' => isset($l['id']) ? Location::where('comic_vine_id', $l['id'])->first() : null,
            ]);

        return view('issues.show', compact('issue', 'descriptionSections', 'linkedTeams', 'linkedLocations'));
    }
    
}