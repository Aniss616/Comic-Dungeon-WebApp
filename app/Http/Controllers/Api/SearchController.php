<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Character;
use App\Models\Volume;

class SearchController extends Controller
{
    public function search()
    {
        $q = request('q');

        return [
            'characters' => Character::where('name', 'like', "%$q%")->get(),
            'volumes'    => Volume::where('name', 'like', "%$q%")->get(),
        ];
    }
}