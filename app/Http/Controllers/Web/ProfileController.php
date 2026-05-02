<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $favouriteCharacters = collect();
        $readIssues          = collect();
        $favouriteIssues     = collect();

        // These will be populated once we build user features
        // $favouriteCharacters = $user->favouriteCharacters();
        // $readIssues          = $user->reads()->with('volume')->get();
        // $favouriteIssues     = $user->favourites()->with('volume')->get();

        return view('profile.index', compact('user', 'favouriteCharacters', 'readIssues', 'favouriteIssues'));
    }
}