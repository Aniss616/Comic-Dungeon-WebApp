<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Issue;
use App\Services\ComicVineService;

class IssueController extends Controller
{
    protected $comicVine;

    public function __construct(ComicVineService $comicVine)
    {
        $this->comicVine = $comicVine;
    }

    // GET /api/issues
    public function index()
    {
        $issues = Issue::with('comic')->get();
        return response()->json($issues);
    }

    // GET /api/issues/{id}
    public function show($id)
    {
        $issue = Issue::with([
            'comic',
            'characters',
            'authors',
            'artists'
        ])->find($id);

        if (!$issue) {
            return response()->json(['message' => 'Issue not found'], 404);
        }

        return response()->json($issue);
    }

    // Fetch from Comic Vine and store locally
    // GET /api/issues/fetch/{comicVineId}
    public function fetchFromApi($comicVineId)
    {
        $existing = Issue::where('comic_vine_id', $comicVineId)->first();
        if ($existing) {
            return response()->json($existing);
        }

        $data = $this->comicVine->getIssue($comicVineId);

        if (!$data || $data['status_code'] !== 1) {
            return response()->json(['message' => 'Issue not found on Comic Vine'], 404);
        }

        $result = $data['results'];

        $issue = Issue::create([
            'comic_vine_id'  => $result['id'],
            'issue_number'   => $result['issue_number'],
            'cover_image'    => $result['image']['original_url'] ?? null,
            'release_date'   => $result['cover_date'] ?? null,
        ]);

        return response()->json($issue);
    }
}