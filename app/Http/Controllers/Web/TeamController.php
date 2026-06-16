<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Team;

class TeamController extends Controller
{
    public function show(Team $team)
    {
        $team->load([
            'characters' => fn ($q) => $q->orderBy('name'),
            'issues.volume',
        ]);

        return view('teams.show', [
            'team' => $team,
            'characters' => $team->characters,
            'issues' => $team->issues,
        ]);
    }
}