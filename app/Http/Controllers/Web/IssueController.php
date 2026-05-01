<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Issue;

class IssueController extends Controller
{
    public function show(int $id)
    {
        $issue = Issue::with(['volume.publisher', 'characters', 'people'])
            ->findOrFail($id);

        return view('issues.show', compact('issue'));
    }
}