<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ComicVineImportService;
use Illuminate\Http\Request;

class ImportController extends Controller
{
    protected ComicVineImportService $importer;

    public function __construct(ComicVineImportService $importer)
    {
        $this->importer = $importer;
    }

    // POST /api/import/characters?limit=10&offset=0
    public function characters(Request $request)
    {
        $limit  = (int) $request->input('limit', 10);
        $offset = (int) $request->input('offset', 0);
        $count  = $this->importer->importCharacters($limit, $offset);

        return response()->json([
            'message' => "Imported {$count} characters",
        ]);
    }

    // POST /api/import/characters/{id}
    public function character(int $id)
    {
        $result = $this->importer->importCharacter($id);

        return response()->json([
            'message' => 'Character imported',
            'data'    => $result,
        ]);
    }

    // POST /api/import/publishers/{id}
    public function publisher(int $id)
    {
        $result = $this->importer->importPublisher($id);

        return response()->json([
            'message' => 'Publisher imported',
            'data'    => $result,
        ]);
    }

    // POST /api/import/volumes?limit=10&offset=0
    public function volumes(Request $request)
    {
        $limit  = (int) $request->input('limit', 10);
        $offset = (int) $request->input('offset', 0);
        $count  = $this->importer->importVolumes($limit, $offset);

        return response()->json([
            'message' => "Imported {$count} volumes",
        ]);
    }

    // POST /api/import/volumes/{id}
    public function volume(int $id)
    {
        $result = $this->importer->importVolume($id);

        return response()->json([
            'message' => 'Volume imported',
            'data'    => $result,
        ]);
    }

    // POST /api/import/volumes/{volumeId}/issues?limit=20
    public function issues(int $volumeId, Request $request)
    {
        $limit = (int) $request->input('limit', 20);
        $count = $this->importer->importIssuesByVolume($volumeId, $limit);

        return response()->json([
            'message' => "Imported {$count} issues for volume {$volumeId}",
        ]);
    }

    // POST /api/import/issues/{id}
    public function issue(int $id)
    {
        $result = $this->importer->importIssue($id);

        return response()->json([
            'message' => 'Issue imported',
            'data'    => $result,
        ]);
    }

    // POST /api/import/persons
public function persons(Request $request)
{
    $limit  = (int) $request->input('limit', 10);
    $offset = (int) $request->input('offset', 0);
    $count  = $this->importer->importPersons($limit, $offset);

    return response()->json([
        'message' => "Imported {$count} people",
    ]);
}

// POST /api/import/persons/{id}
public function person(int $id)
{
    $result = $this->importer->importPerson($id);

    return response()->json([
        'message' => 'Person imported',
        'data'    => $result,
    ]);
}
}