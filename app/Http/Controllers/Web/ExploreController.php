<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Character;
use App\Models\Volume;
use App\Models\Issue;

class ExploreController extends Controller
{
    public function index()
    {
        $q    = request('q');
        $tab  = request('tab', 'characters');

        $characters = collect();
        $volumes    = collect();
        $issues     = collect();

        if ($q) {
            // Search mode
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
                ->orWhereHas('volume', function($query) use ($q) {
                    $query->where('name', 'like', "%$q%");
                })
                ->orderBy('name')
                ->paginate(24)
                ->withQueryString();
            }
        } else {
            // Default random mode
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
            }
        }

        return view('explore.index', compact('characters', 'volumes', 'issues', 'q', 'tab'));
    }
}