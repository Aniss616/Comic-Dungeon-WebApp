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

    public function characters(Request $request)
    {
        $limit = (int) $request->input('limit', 10);
        $offset = (int) $request->input('offset', 0);

        $count = $this->importer->importCharacters($limit, $offset);

        return response()->json([
            'message' => "Imported {$count} characters",
        ]);
    }

    public function character($id)
    {
        $result = $this->importer->importCharacter($id);

        return response()->json([
            'message' => 'Character imported',
            'data' => $result,
        ]);
    }
}