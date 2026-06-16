<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Character;
use App\Models\Volume;
use App\Models\Issue;
use App\Models\StoryArc;
use App\Models\Team;
use App\Models\Location;

class ExploreController extends Controller
{
    public function index()
    {
        $q   = request('q');
        $tab = request('tab', 'characters');
        $characters = collect();
        $volumes    = collect();
        $issues     = collect();
        $storyArcs  = collect();
        $teams      = collect();
        $locations = collect();
        if ($q) {
            if ($tab === 'characters') {
                $characters = Character::where('name', 'like', "%$q%")
                    ->orderBy('name')
                    ->paginate(24)
                    ->withQueryString();
            } elseif ($tab === 'volumes') {
                $volumes = Volume::with('publisher')
                    ->where('name', 'like', "%$q%")
                    ->orderBy('name')
                    ->paginate(24)
                    ->withQueryString();
            } elseif ($tab === 'issues') {
                $issues = Issue::with('volume')
                    ->where('name', 'like', "%$q%")
                    ->orWhereHas('volume', function ($query) use ($q) {
                        $query->where('name', 'like', "%$q%");
                    })
                    ->orderBy('name')
                    ->paginate(24)
                    ->withQueryString();
            } elseif ($tab === 'arcs') {
                $storyArcs = StoryArc::where('name', 'like', "%$q%")
                    ->withCount('issues')
                    ->orderBy('name')
                    ->paginate(24)
                    ->withQueryString();
            } elseif ($tab === 'teams') {
                $teams = Team::where('name', 'like', "%$q%")
                    ->withCount(['characters', 'issues'])
                    ->orderBy('name')
                    ->paginate(24)
                    ->withQueryString();
            }elseif ($tab === 'locations') {
                $locations = Location::where('name', 'like', "%$q%")
                    ->withCount('issues')
                    ->orderBy('name')
                    ->paginate(24)
                    ->withQueryString();
            }
            }else {
            if ($tab === 'characters') {
                $characters = Character::whereNotNull('image')
                    ->inRandomOrder()
                    ->paginate(24)
                    ->withQueryString();
            } elseif ($tab === 'volumes') {
                $volumes = Volume::with('publisher')
                    ->whereNotNull('cover_image')
                    ->inRandomOrder()
                    ->paginate(24)
                    ->withQueryString();
            } elseif ($tab === 'issues') {
                $issues = Issue::with('volume')
                    ->whereNotNull('image')
                    ->inRandomOrder()
                    ->paginate(24)
                    ->withQueryString();
            } elseif ($tab === 'arcs') {
                $storyArcs = StoryArc::withCount('issues')
                    ->having('issues_count', '>', 0)
                    ->inRandomOrder()
                    ->paginate(24)
                    ->withQueryString();
            } elseif ($tab === 'teams') {
                $teams = Team::withCount(['characters', 'issues'])
                    ->having('issues_count', '>', 0)
                    ->inRandomOrder()
                    ->paginate(24)
                    ->withQueryString();
            }elseif ($tab === 'locations') {
                $locations = Location::withCount('issues')
                    ->having('issues_count', '>', 0)
                    ->inRandomOrder()
                    ->paginate(24)
                    ->withQueryString();
            }
        }
        return view('explore.index', compact(
            'characters', 'volumes', 'issues', 'storyArcs', 'teams', 'q', 'tab', 'locations'
        ));
    }
}