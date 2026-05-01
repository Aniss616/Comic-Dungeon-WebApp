<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Character;
use App\Models\Volume;

class SearchController extends Controller
{
    public function index()
    {
        $q = request('q');

        if (!$q) {
            return view('search.index', [
                'characters' => collect(),
                'volumes'    => collect(),
                'q'          => null,
            ]);
        }

        $characters = Character::where('name', 'like', "%$q%")
            ->orderBy('name')
            ->get();

        $volumes = Volume::with('publisher')
            ->where('name', 'like', "%$q%")
            ->orderBy('name')
            ->get();

        return view('search.index', compact('characters', 'volumes', 'q'));
    }
}