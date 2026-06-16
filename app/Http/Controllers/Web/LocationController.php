<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Location;

class LocationController extends Controller
{
    public function show(Location $location)
    {
        $location->load('issues.volume');

        return view('locations.show', [
            'location' => $location,
            'issues' => $location->issues,
        ]);
    }
}