<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Issue;

class IssueController extends Controller
{
    public function show($id)
    {
        return Issue::with(['volume', 'people', 'characters'])
            ->findOrFail($id);
    }
}