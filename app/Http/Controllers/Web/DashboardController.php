<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Character;
use App\Models\Issue;
use App\Models\Person;
use App\Models\Publisher;
use App\Models\Volume;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'characters' => Character::count(),
            'volumes'    => Volume::count(),
            'issues'     => Issue::count(),
            'publishers' => Publisher::count(),
            'people'     => Person::count(),
        ];

        $recentVolumes    = Volume::with('publisher')->latest()->take(5)->get();
        $recentCharacters = Character::latest()->take(5)->get();

        return view('dashboard.index', compact('stats', 'recentVolumes', 'recentCharacters'));
    }
}