<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Issue;
use App\Models\Character;
use Illuminate\Support\Facades\Auth;

class UserActionController extends Controller
{
    // Toggle read status on an issue
    public function toggleRead(Issue $issue)
    {
        $user   = Auth::user();
        $exists = $user->reads()->where('issue_id', $issue->id)->exists();

        if ($exists) {
            $user->reads()->detach($issue->id);
            $status = 'unread';
        } else {
            $user->reads()->attach($issue->id, ['read_date' => now()]);
            $status = 'read';
        }

        return response()->json(['status' => $status]);
    }

    // Toggle favourite on an issue
    public function toggleFavourite(Issue $issue)
    {
        $user   = Auth::user();
        $exists = $user->favourites()->where('issue_id', $issue->id)->exists();

        if ($exists) {
            $user->favourites()->detach($issue->id);
            $status = 'unfavourited';
        } else {
            $user->favourites()->attach($issue->id, ['favourite_date' => now()]);
            $status = 'favourited';
        }

        return response()->json(['status' => $status]);
    }

    // Toggle favourite on a character
    public function toggleFavouriteCharacter(int $characterId)
    {
        $user   = Auth::user();
        $exists = $user->favouriteCharacters()->where('character_id', $characterId)->exists();

        if ($exists) {
            $user->favouriteCharacters()->detach($characterId);
            $status = 'unfavourited';
        } else {
            $user->favouriteCharacters()->attach($characterId);
            $status = 'favourited';
        }

        return response()->json(['status' => $status]);
    }
}