<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Volume;

class VolumeController extends Controller
{
    public function index()
    {
        return Volume::with('publisher')->paginate(20);
    }

    public function show($id)
    {
        return Volume::with('issues')->findOrFail($id);
    }
}