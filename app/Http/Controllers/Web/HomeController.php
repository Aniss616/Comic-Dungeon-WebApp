<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Character;
use App\Models\Volume;
use App\Models\Issue;

class HomeController extends Controller
{
    public function index()
    {
        $featuredCharacters = Character::whereNotNull('image')
            ->inRandomOrder()
            ->take(6)
            ->get();

        $featuredVolumes = Volume::with('publisher')
            ->whereNotNull('cover_image')
            ->inRandomOrder()
            ->take(6)
            ->get();

        return view('home.index', compact('featuredCharacters', 'featuredVolumes'));
    }
}