<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Issue;
use App\Traits\ParsesDescription;
use App\Models\Team;

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
            ->map(fn ($t) => [
                'name' => $t['name'],
                'team' => isset($t['id']) ? Team::where('comic_vine_id', $t['id'])->first() : null,
            ]);

        return view('issues.show', compact('issue', 'descriptionSections', 'linkedTeams'));
    }
    
}