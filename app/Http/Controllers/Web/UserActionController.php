<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Issue;
use App\Models\Character;
use Illuminate\Support\Facades\Auth;

class UserActionController extends Controller
{
    // Toggle read status on an issue
    public function toggleRead(int $issueId)
    {
        $user   = Auth::user();
        $exists = $user->reads()->where('issue_id', $issueId)->exists();

        if ($exists) {
            $user->reads()->detach($issueId);
            $status = 'unread';
        } else {
            $user->reads()->attach($issueId, [
                'read_date' => now()->toDateString(),
            ]);
            $status = 'read';
        }

        return response()->json(['status' => $status]);
    }

    // Toggle favourite on an issue
    public function toggleFavouriteIssue(int $issueId)
    {
        $user   = Auth::user();
        $exists = $user->favourites()->where('issue_id', $issueId)->exists();

        if ($exists) {
            $user->favourites()->detach($issueId);
            $status = 'unfavourited';
        } else {
            $user->favourites()->attach($issueId, [
                'favourite_date' => now()->toDateString(),
            ]);
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