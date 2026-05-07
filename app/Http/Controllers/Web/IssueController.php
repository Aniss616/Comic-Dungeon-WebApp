<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Issue;
use App\Traits\ParsesDescription;

class IssueController extends Controller
{
    use ParsesDescription;
    
    public function show(int $id)
    {
        $issue = Issue::with(['volume.publisher', 'characters', 'people'])
            ->findOrFail($id);

        $descriptionSections = $issue->description
            ? $this->parseDescription($issue->description)
            : [];

        return view('issues.show', compact('issue', 'descriptionSections'));
    }

}