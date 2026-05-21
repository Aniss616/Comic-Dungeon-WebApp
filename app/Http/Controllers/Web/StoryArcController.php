<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\StoryArc;

class StoryArcController extends Controller
{
    public function show(StoryArc $storyArc)
    {
        $issues = $storyArc->issues()
            ->with('volume')
            ->orderBy('cover_date')
            ->orderBy('issue_number')
            ->get();

        return view('story-arcs.show', compact('storyArc', 'issues'));
    }
}