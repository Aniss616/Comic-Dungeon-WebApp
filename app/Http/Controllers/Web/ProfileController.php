<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $favouriteCharacters = $user->favouriteCharacters()->get();

        $readIssues = $user->reads()
            ->with('volume')
            ->orderByPivot('read_date', 'desc')
            ->get();

        $favouriteIssues = $user->favourites()
            ->with('volume')
            ->orderByPivot('favourite_date', 'desc')
            ->get();

        return view('profile.index', compact('user', 'favouriteCharacters', 'readIssues', 'favouriteIssues'));
    }
}