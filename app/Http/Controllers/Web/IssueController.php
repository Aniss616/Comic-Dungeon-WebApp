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

        $descriptionSections = $issue->description
            ? $this->parseDescription($issue->description)
            : [];

        return view('issues.show', compact('issue', 'descriptionSections'));
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
            return [['title' => null, 'content' => strip_tags($html)]];
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