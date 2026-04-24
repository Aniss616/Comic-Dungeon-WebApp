<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class ComicVineService
{
    protected $apiKey;
    protected $baseUrl;

    public function __construct()
    {
        $this->apiKey = env('COMIC_VINE_API_KEY');
        $this->baseUrl = env('COMIC_VINE_BASE_URL');
    }

    private function get($endpoint, $params = [])
    {
        $response = Http::get("{$this->baseUrl}/{$endpoint}", array_merge([
            'api_key'  => $this->apiKey,
            'format'   => 'json',
        ], $params));

        return $response->json();
    }

    // Get a single character by Comic Vine ID
    public function getCharacter($id)
    {
        return $this->get("character/4005-{$id}", [
            'field_list' => 'id,name,aliases,deck,image,first_appeared_in_issue,powers'
        ]);
    }

    // Get all characters (paginated)
    public function getCharacters($limit = 10, $offset = 0)
    {
        return $this->get('characters', [
            'limit'      => $limit,
            'offset'     => $offset,
            'field_list' => 'id,name,aliases,deck,image,first_appeared_in_issue'
        ]);
    }

    // Get a single comic series by ID
    public function getComic($id)
    {
        return $this->get("volume/4050-{$id}", [
            'field_list' => 'id,name,description,image,start_year,publisher,genres'
        ]);
    }

    // Get all comics (paginated)
    public function getComics($limit = 10, $offset = 0)
    {
        return $this->get('volumes', [
            'limit'      => $limit,
            'offset'     => $offset,
            'field_list' => 'id,name,description,image,start_year,publisher'
        ]);
    }

    // Get a single issue by ID
    public function getIssue($id)
    {
        return $this->get("issue/4000-{$id}", [
            'field_list' => 'id,name,issue_number,image,cover_date,volume,character_credits,person_credits'
        ]);
    }

    // Get issues by comic/volume ID
    public function getIssuesByComic($comicId, $limit = 10)
    {
        return $this->get('issues', [
            'filter'     => "volume:{$comicId}",
            'limit'      => $limit,
            'field_list' => 'id,name,issue_number,image,cover_date,character_credits'
        ]);
    }

    // Get a publisher by ID
    public function getPublisher($id)
    {
        return $this->get("publisher/4010-{$id}", [
            'field_list' => 'id,name,image,deck,location_address'
        ]);
    }

    // Search for anything
    public function search($query, $limit = 10)
    {
        return $this->get('search', [
            'query'      => $query,
            'limit'      => $limit,
            'resources'  => 'character,volume,issue',
            'field_list' => 'id,name,image,deck,resource_type'
        ]);
    }
}