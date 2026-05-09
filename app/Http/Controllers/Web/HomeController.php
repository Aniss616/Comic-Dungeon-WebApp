<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Character;
use App\Models\Volume;
use App\Models\Issue;
use App\Services\RecommendationService;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function __construct(
        protected RecommendationService $recommendations
    ) {}

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

        $recommendedIssues = collect();

        if (Auth::check()) {
            $recommendedIssues = $this->recommendations->getRecommendations(Auth::user(), 12);
        }

        return view('home.index', compact(
            'featuredCharacters',
            'featuredVolumes',
            'recommendedIssues',
        ));
    }
}