<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Volume;

class VolumeController extends Controller
{
    public function index()
    {
        $volumes = Volume::with('publisher')
            ->orderBy('name')
            ->paginate(24);

        return view('volumes.index', compact('volumes'));
    }

    public function show(int $id)
    {
        $volume = Volume::with(['publisher', 'issues' => function($q) {
            $q->orderBy('issue_number');
        }])->findOrFail($id);

        return view('volumes.show', compact('volume'));
    }
}