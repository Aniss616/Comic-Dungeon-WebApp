<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class ComicVineService
{
    protected string $apiKey;
    protected string $baseUrl;

    protected array $fields = [
        'character' => 'id,name,aliases,deck,description,image,real_name,birth,gender,origin,publisher,first_appeared_in_issue,powers,teams,character_friends,character_enemies',
        'volume' => 'id,name,description,image,start_year,publisher,count_of_issues,first_issue,last_issue,site_detail_url',
        'issue'  => 'id,name,issue_number,description,image,cover_date,store_date,volume,character_credits,person_credits,team_credits,location_credits,story_arc_credits,site_detail_url',
        'publisher' => 'id,name,image,deck,location_city,location_state,location_country,aliases',
        'person' => 'id,name,deck,image,birth,country',
    ];

    public function __construct()
    {
        $this->apiKey = env('COMIC_VINE_API_KEY');
        $this->baseUrl = rtrim(env('COMIC_VINE_BASE_URL'), '/');
    }

    private function get(string $endpoint, array $params = [])
    {
        usleep(1100000);
        $url = "{$this->baseUrl}/{$endpoint}/";

        return Http::withoutVerifying()
            ->timeout(60)
            ->get($url, array_merge([
                'api_key' => $this->apiKey,
                'format'  => 'json',
            ], $params))
            ->json();
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
            'person' => "4040-{$id}",
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

    //people
    public function getPerson(int $id)
{
    return $this->get("person/{$this->buildResource('person', $id)}", [
        'field_list' => $this->fields['person'],
    ]);
}

public function getPersons(int $limit = 10, int $offset = 0)
{
    return $this->get('people', [
        'limit'      => $limit,
        'offset'     => $offset,
        'field_list' => $this->fields['person'],
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