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

        $descriptionSections = $volume->description
            ? $this->parseDescription($volume->description)
            : [];

        return view('volumes.show', compact('volume', 'descriptionSections'));
    }

    private function parseDescription(string $html): array
    {
        $sections = [];

        if (empty($html)) return $sections;

        $dom = new \DOMDocument();
        @$dom->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));
        $xpath    = new \DOMXPath($dom);
        $headings = $xpath->query('//h2|//h3|//h4');

        if ($headings->length === 0) {
            // No headings — split by paragraphs instead
            $paragraphs = $xpath->query('//p');
            if ($paragraphs->length > 0) {
                foreach ($paragraphs as $p) {
                    $content = trim(strip_tags($dom->saveHTML($p)));
                    if (!empty($content)) {
                        $sections[] = ['title' => null, 'content' => $content];
                    }
                }
            } else {
                $sections[] = ['title' => null, 'content' => strip_tags($html)];
            }
            return $sections;
        }

        foreach ($headings as $heading) {
            $title   = trim($heading->textContent);
            $content = '';
            $sibling = $heading->nextSibling;

            while ($sibling) {
                if (in_array($sibling->nodeName, ['h2', 'h3', 'h4'])) break;
                $content .= $dom->saveHTML($sibling);
                $sibling  = $sibling->nextSibling;
            }

            $content = trim(strip_tags($content));
            if (!empty($content)) {
                $sections[] = ['title' => $title, 'content' => $content];
            }
        }

        return $sections;
    }
}