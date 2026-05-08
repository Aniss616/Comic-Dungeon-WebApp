<?php

namespace App\Services;

use App\Models\Character;
use App\Models\Issue;
use App\Models\Person;
use App\Models\Publisher;
use App\Models\Volume;

class ComicVineImportService
{
    protected ComicVineService $api;
    protected ComicVineService $comicVine;

    public function __construct(ComicVineService $api, ComicVineService $comicVine)
    {
        $this->api = $api;
        $this->comicVine = $comicVine;
    }

    /* =========================
        CHARACTERS
    ========================= */

    public function importCharacters(int $limit = 10, int $offset = 0)
    {
        $response = $this->api->getCharacters($limit, $offset);
        $results  = $response['results'] ?? [];
        $imported = 0;

        foreach ($results as $data) {
            $this->saveCharacter($data);
            $imported++;
        }

        return $imported;
    }

    public function importCharacter(int $comicVineId)
    {
        $response = $this->api->getCharacter($comicVineId);
        $data     = $response['results'] ?? null;

        if (!$data) return null;

        return $this->saveCharacter($data);
    }

    private function saveCharacter(array $data): Character
    {
        return Character::updateOrCreate(
            ['comic_vine_id' => $data['id']],
            [
                'name'                    => $data['name'] ?? null,
                'real_name'               => $data['real_name'] ?? null,
                'description'             => $data['description'] ?? null,
                'aliases'                 => $this->parseAliases($data['aliases'] ?? null),
                'image'                   => $data['image']['original_url'] ?? null,
                'birth'                   => $data['birth'] ?? null,
                'gender'                  => $data['gender'] ?? null,
                'origin'                  => $data['origin']['name'] ?? null,
                'publisher'               => $data['publisher']['name'] ?? null,
                'powers'                  => $this->parsePowers($data['powers'] ?? []),
                'teams'                   => $this->parseNameList($data['teams'] ?? []),
                'character_friends'       => $this->parseNameList($data['character_friends'] ?? []),
                'character_enemies'       => $this->parseNameList($data['character_enemies'] ?? []),
                'first_appeared_in_issue' => $data['first_appeared_in_issue'] ?? null,
            ]
        );
    }

    /* =========================
        PUBLISHERS
    ========================= */

    public function importPublisher(int $comicVineId)
    {
        $response = $this->comicVine->getPublisher($comicVineId);
        $data     = $response['results'] ?? null;

        if (!$data) return null;

        return Publisher::updateOrCreate(
            ['comic_vine_id' => $data['id']],
            [
                'name'             => $data['name'] ?? null,
                'description'      => $data['deck'] ?? null,
                'logo_url'         => $data['image']['original_url'] ?? null,
                'location_city'    => $data['location_city'] ?? null,
                'location_state'   => $data['location_state'] ?? null,
                'location_country' => $data['location_country'] ?? null,
                'aliases'          => $this->parseAliases($data['aliases'] ?? null),
            ]
        );
    }

    /* =========================
        VOLUMES
    ========================= */

    public function importVolumes(int $limit = 10, int $offset = 0)
    {
        $response = $this->comicVine->getVolumes($limit, $offset);
        if (!isset($response['results'])) return 0;

        $imported = 0;

        foreach ($response['results'] as $data) {
            $publisherId = null;
            if (!empty($data['publisher'])) {
                $publisher   = $this->importPublisher($data['publisher']['id']);
                $publisherId = $publisher?->id;
            }

            Volume::updateOrCreate(
                ['comic_vine_id' => $data['id']],
                [
                    'name'            => $data['name'] ?? null,
                    'description'     => $data['description'] ?? null,
                    'cover_image'     => $data['image']['original_url'] ?? null,
                    'count_of_issues' => $data['count_of_issues'] ?? null,
                    'first_issue'     => $data['first_issue'] ?? null,
                    'last_issue'      => $data['last_issue'] ?? null,
                    'site_detail_url' => $data['site_detail_url'] ?? null,
                    'publisher_id'    => $publisherId,
                ]
            );

            $imported++;
        }

        return $imported;
    }

    public function importVolume(int $comicVineId)
    {
        $response = $this->comicVine->getVolume($comicVineId);
        $data     = $response['results'] ?? null;

        if (!$data) return null;

        $publisherId = null;
        if (!empty($data['publisher'])) {
            $publisher   = $this->importPublisher($data['publisher']['id']);
            $publisherId = $publisher?->id;
        }

        return Volume::updateOrCreate(
            ['comic_vine_id' => $data['id']],
            [
                'name'            => $data['name'] ?? null,
                'description'     => $data['description'] ?? null,
                'cover_image'     => $data['image']['original_url'] ?? null,
                'count_of_issues' => $data['count_of_issues'] ?? null,
                'first_issue'     => $data['first_issue'] ?? null,
                'last_issue'      => $data['last_issue'] ?? null,
                'site_detail_url' => $data['site_detail_url'] ?? null,
                'publisher_id'    => $publisherId,
            ]
        );
    }

    /* =========================
        ISSUES
    ========================= */

    public function importIssuesByVolume(int $volumeId, int $limit = 20)
    {
    $volume = $this->importVolume($volumeId);
    if (!$volume) return 0;

    // Get list of issue IDs for this volume
    $response = $this->comicVine->getIssuesByVolume($volumeId, $limit);
        if (!isset($response['results'])) return 0;

    $imported = 0;

        foreach ($response['results'] as $data) {
            // Fetch full issue data to get character_credits, person_credits etc.
            $fullResponse = $this->comicVine->getIssue($data['id']);
            $fullData     = $fullResponse['results'] ?? null;

            if (!$fullData) continue;

            $issue = Issue::updateOrCreate(
                ['comic_vine_id' => $fullData['id']],
                [
                    'name'              => $fullData['name'] ?? null,
                    'issue_number'      => $fullData['issue_number'] ?? null,
                    'description'       => $fullData['description'] ?? null,
                    'image'             => $fullData['image']['original_url'] ?? null,
                    'cover_date'        => $fullData['cover_date'] ?? null,
                    'store_date'        => $fullData['store_date'] ?? null,
                    'teams'             => $this->parseNameList($fullData['team_credits'] ?? []),
                    'locations'         => $this->parseNameList($fullData['location_credits'] ?? []),
                    'story_arc_credits' => $this->parseNameList($fullData['story_arc_credits'] ?? []),
                    'site_detail_url' => $fullData['site_detail_url'] ?? null,
                    'volume_id'         => $volume->id,
                ]
            );

        // Attach characters
            if (!empty($fullData['character_credits'])) {
                foreach ($fullData['character_credits'] as $charData) {
                    $character = Character::firstOrCreate(
                        ['comic_vine_id' => $charData['id']],
                        ['name' => $charData['name'] ?? null]
                    );
                    $issue->characters()->syncWithoutDetaching($character->id);
                }
            }

        // Attach people with role
            if (!empty($fullData['person_credits'])) {
                foreach ($fullData['person_credits'] as $personData) {
                    $person = Person::firstOrCreate(
                        ['comic_vine_id' => $personData['id']],
                        ['name' => $personData['name'] ?? null]
                    );
                    $issue->people()->syncWithoutDetaching([
                        $person->id => ['role' => $personData['role'] ?? null]
                    ]);
                }
            }

         $imported++;
        }

        return $imported;
    }

    public function importIssue(int $comicVineId)
    {
        $response = $this->comicVine->getIssue($comicVineId);
        $data     = $response['results'] ?? null;

        if (!$data) return null;

        $volumeId = null;
        if (!empty($data['volume'])) {
            $volume   = $this->importVolume($data['volume']['id']);
            $volumeId = $volume?->id;
        }

        $issue = Issue::updateOrCreate(
            ['comic_vine_id' => $data['id']],
            [
                'name'              => $data['name'] ?? null,
                'issue_number'      => $data['issue_number'] ?? null,
                'description'       => $data['description'] ?? null,
                'image'             => $data['image']['original_url'] ?? null,
                'cover_date'        => $data['cover_date'] ?? null,
                'store_date'        => $data['store_date'] ?? null,
                'teams'             => $this->parseNameList($data['team_credits'] ?? []),
                'locations'         => $this->parseNameList($data['location_credits'] ?? []),
                'story_arc_credits' => $this->parseNameList($data['story_arc_credits'] ?? []),
                'site_detail_url' => $fullData['site_detail_url'] ?? null,
                'volume_id'         => $volumeId,
            ]
        );

        // Attach characters
        if (!empty($data['character_credits'])) {
            foreach ($data['character_credits'] as $charData) {
                $character = Character::firstOrCreate(
                    ['comic_vine_id' => $charData['id']],
                    ['name' => $charData['name'] ?? null]
                );
                $issue->characters()->syncWithoutDetaching($character->id);
            }
        }

        // Attach people with role
        if (!empty($data['person_credits'])) {
            foreach ($data['person_credits'] as $personData) {
                $person = Person::firstOrCreate(
                    ['comic_vine_id' => $personData['id']],
                    ['name' => $personData['name'] ?? null]
                );
                $issue->people()->syncWithoutDetaching([
                    $person->id => ['role' => $personData['role'] ?? null]
                ]);
            }
        }

        return $issue;
    }

    /* =========================
        PEOPLE
    ========================= */

    public function importPersons(int $limit = 10, int $offset = 0)
    {
        $response = $this->comicVine->getPersons($limit, $offset);
        if (!isset($response['results'])) return 0;

        $imported = 0;

        foreach ($response['results'] as $data) {
            Person::updateOrCreate(
                ['comic_vine_id' => $data['id']],
                [
                    'name'        => $data['name'] ?? null,
                    'description' => $data['deck'] ?? null,
                ]
            );
            $imported++;
        }

        return $imported;
    }

    public function importPerson(int $comicVineId)
    {
        $response = $this->comicVine->getPerson($comicVineId);
        $data     = $response['results'] ?? null;

        if (!$data) return null;

        return Person::updateOrCreate(
            ['comic_vine_id' => $data['id']],
            [
                'name'        => $data['name'] ?? null,
                'description' => $data['deck'] ?? null,
            ]
        );
    }

    /* =========================
        HELPERS
    ========================= */

    private function parseAliases(?string $aliases): array
    {
        if (!$aliases) return [];

        return array_values(array_filter(
            array_map('trim', explode("\n", $aliases))
        ));
    }

    private function parsePowers(array $powers): array
    {
        return array_values(array_map(fn($p) => $p['name'], $powers));
    }

    private function parseNameList(array $items): array
    {
        return array_values(array_map(fn($i) => [
            'id'   => $i['id'],
            'name' => $i['name'],
        ], $items));
    }
}