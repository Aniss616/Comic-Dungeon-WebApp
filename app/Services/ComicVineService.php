<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class ComicVineService
{
    protected string $apiKey;
    protected string $baseUrl;

    protected array $fields = [
        'character' => 'id,name,aliases,deck,image,first_appeared_in_issue',
        'volume'    => 'id,name,description,image,start_year,publisher',
        'issue'     => 'id,name,issue_number,description,image,cover_date,volume,character_credits,person_credits',
        'publisher' => 'id,name,image,deck,location_address',
    ];

    public function __construct()
    {
        $this->apiKey = env('COMIC_VINE_API_KEY');
        $this->baseUrl = rtrim(env('COMIC_VINE_BASE_URL'), '/');
    }

    private function get(string $endpoint, array $params = [])
    {
        $response = Http::timeout(15)->get("{$this->baseUrl}/{$endpoint}", array_merge([
            'api_key' => $this->apiKey,
            'format'  => 'json',
        ], $params));

        return $response->json();
    }

    /**
     * Build ComicVine resource ID format (e.g. 4005-123)
     */
    private function buildResource(string $type, int $id): string
    {
        return match ($type) {
            'character' => "4005-{$id}",
            'volume'    => "4050-{$id}",
            'issue'     => "4000-{$id}",
            'publisher' => "4010-{$id}",
            default     => $id,
        };
    }

    /* =========================
        CHARACTERS
    ========================= */

    public function getCharacter(int $id)
    {
        return $this->get("character/{$this->buildResource('character', $id)}", [
            'field_list' => $this->fields['character'],
        ]);
    }

    public function getCharacters(int $limit = 10, int $offset = 0)
    {
        return $this->get('characters', [
            'limit'      => $limit,
            'offset'     => $offset,
            'field_list' => $this->fields['character'],
        ]);
    }

    /* =========================
        VOLUMES (COMICS)
    ========================= */

    public function getVolume(int $id)
    {
        return $this->get("volume/{$this->buildResource('volume', $id)}", [
            'field_list' => $this->fields['volume'],
        ]);
    }

    public function getVolumes(int $limit = 10, int $offset = 0)
    {
        return $this->get('volumes', [
            'limit'      => $limit,
            'offset'     => $offset,
            'field_list' => $this->fields['volume'],
        ]);
    }

    /* =========================
        ISSUES
    ========================= */

    public function getIssue(int $id)
    {
        return $this->get("issue/{$this->buildResource('issue', $id)}", [
            'field_list' => $this->fields['issue'],
        ]);
    }

    public function getIssuesByVolume(int $volumeId, int $limit = 10)
    {
        return $this->get('issues', [
            'filter'     => "volume:{$volumeId}",
            'limit'      => $limit,
            'field_list' => $this->fields['issue'],
        ]);
    }

    /* =========================
        PUBLISHERS
    ========================= */

    public function getPublisher(int $id)
    {
        return $this->get("publisher/{$this->buildResource('publisher', $id)}", [
            'field_list' => $this->fields['publisher'],
        ]);
    }

    /* =========================
        SEARCH
    ========================= */

    public function search(string $query, int $limit = 10)
    {
        return $this->get('search', [
            'query'      => $query,
            'limit'      => $limit,
            'resources'  => 'character,volume,issue,publisher',
            'field_list' => 'id,name,image,deck,resource_type',
        ]);
    }
}