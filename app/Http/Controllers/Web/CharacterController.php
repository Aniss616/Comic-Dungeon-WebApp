<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Character;

class CharacterController extends Controller
{
    public function index()
    {
        $characters = Character::orderBy('name')
            ->paginate(24);

        return view('characters.index', compact('characters'));
    }

    public function show(int $id)
{
    $character = Character::with('issues.volume')
        ->findOrFail($id);

    $issues = $character->issues->sortBy([
        ['volume_id', 'asc'],
        ['issue_number', 'asc'],
    ])->values();

    $firstAppearance = $character->issues
        ->whereNotNull('cover_date')
        ->sortBy('cover_date')
        ->first() ?? $issues->first();

    $bestStart = $issues->firstWhere('issue_number', 1) ?? $issues->first();

    $descriptionSections = $character->description
        ? $this->parseDescription($character->description)
        : [];

    return view('characters.show', compact(
        'character',
        'issues',
        'firstAppearance',
        'bestStart',
        'descriptionSections'
    ));
}

    private function parseDescription(string $html): array
{
    $sections = [];
    
    if (empty($html)) return $sections;

    // Load HTML
    $dom = new \DOMDocument();
    @$dom->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));
    $xpath = new \DOMXPath($dom);

    // Find all h2/h3/h4 headings
    $headings = $xpath->query('//h2|//h3|//h4');

    if ($headings->length === 0) {
        // No headings — just return as single block
        return [['title' => null, 'content' => strip_tags($html)]];
    }

    foreach ($headings as $heading) {
        $title   = trim($heading->textContent);
        $content = '';

        // Get all sibling nodes until next heading
        $sibling = $heading->nextSibling;
        while ($sibling) {
            if ($sibling->nodeName === 'h2' ||
                $sibling->nodeName === 'h3' ||
                $sibling->nodeName === 'h4') {
                break;
            }
            $content .= $dom->saveHTML($sibling);
            $sibling = $sibling->nextSibling;
        }

        $content = trim(strip_tags($content));

        if (!empty($content)) {
            $sections[] = [
                'title'   => $title,
                'content' => $content,
            ];
        }
    }

    return $sections;
}
}