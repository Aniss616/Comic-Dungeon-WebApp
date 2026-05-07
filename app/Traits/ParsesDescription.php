<?php

namespace App\Traits;

trait ParsesDescription
{
    private function cleanText(string $text): string
    {
        // Decode HTML entities
        $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');

        // Remove zero-width spaces and non-breaking spaces
        $text = str_replace(["\xc2\xa0", "\xe2\x80\x8b", '&nbsp;'], ' ', $text);

        // Normalize whitespace — collapse multiple spaces into one
        $text = preg_replace('/\s+/', ' ', $text);

        // Fix missing spaces after punctuation
        $text = preg_replace('/([.!?])([A-Z])/', '$1 $2', $text);

        return trim($text);
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
            $paragraphs = $xpath->query('//p');
            if ($paragraphs->length > 0) {
                foreach ($paragraphs as $p) {
                    $content = $this->cleanText(strip_tags($dom->saveHTML($p)));
                    if (!empty($content)) {
                        $sections[] = ['title' => null, 'content' => $content];
                    }
                }
            } else {
                $content = $this->cleanText(strip_tags($html));
                if (!empty($content)) {
                    $sections[] = ['title' => null, 'content' => $content];
                }
            }
            return $sections;
        }

        foreach ($headings as $heading) {
            $title   = $this->cleanText($heading->textContent);
            $content = '';
            $sibling = $heading->nextSibling;

            while ($sibling) {
                if (in_array($sibling->nodeName, ['h2', 'h3', 'h4'])) break;
                $content .= $dom->saveHTML($sibling);
                $sibling  = $sibling->nextSibling;
            }

            $content = $this->cleanText(strip_tags($content));
            if (!empty($content)) {
                $sections[] = ['title' => $title, 'content' => $content];
            }
        }

        return $sections;
    }
}