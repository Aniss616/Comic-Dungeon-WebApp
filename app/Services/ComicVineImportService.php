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
            Character::updateOrCreate(
                ['comic_vine_id' => $data['id']],
                [
                    'name'        => $data['name'] ?? null,
                    'description' => $data['deck'] ?? null,
                    'aliases'     => $this->parseAliases($data['aliases'] ?? null),
                    'image'       => $data['image']['original_url'] ?? null,
                ]
            );
            $imported++;
        }

        return $imported;
    }

    public function importCharacter(int $comicVineId)
    {
        $response = $this->api->getCharacter($comicVineId);
        $data     = $response['results'] ?? null;

        if (!$data) return null;

        return Character::updateOrCreate(
            ['comic_vine_id' => $data['id']],
            [
                'name'        => $data['name'] ?? null,
                'description' => $data['deck'] ?? null,
                'aliases'     => $this->parseAliases($data['aliases'] ?? null),
                'image'       => $data['image']['original_url'] ?? null,
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
                'name'        => $data['name'] ?? null,
                'description' => $data['deck'] ?? null,
                'logo_url'    => $data['image']['original_url'] ?? null,
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
                    'name'         => $data['name'] ?? null,
                    'description'  => $data['description'] ?? null,
                    'cover_image'  => $data['image']['original_url'] ?? null,
                    'publisher_id' => $publisherId,
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
                'name'         => $data['name'] ?? null,
                'description'  => $data['description'] ?? null,
                'cover_image'  => $data['image']['original_url'] ?? null,
                'publisher_id' => $publisherId,
            ]
        );
    }

    /* =========================
        ISSUES
    ========================= */

    public function importIssuesByVolume(int $volumeId, int $limit = 20)
    {
        // 1. Fetch and persist volume first
        $volume = $this->importVolume($volumeId);
        if (!$volume) return 0;

        // 2. Fetch issues for this volume
        $response = $this->comicVine->getIssuesByVolume($volumeId, $limit);
        if (!isset($response['results'])) return 0;

        $imported = 0;

        foreach ($response['results'] as $data) {
            // 3. Save issue
            $issue = Issue::updateOrCreate(
                ['comic_vine_id' => $data['id']],
                [
                    'name'         => $data['name'] ?? null,
                    'issue_number' => $data['issue_number'] ?? null,
                    'description'  => $data['description'] ?? null,
                    'image'        => $data['image']['original_url'] ?? null,
                    'cover_date'   => $data['cover_date'] ?? null,
                    'volume_id'    => $volume->id,
                ]
            );

            // 4. Attach characters
            if (!empty($data['character_credits'])) {
                foreach ($data['character_credits'] as $charData) {
                    $character = Character::firstOrCreate(
                        ['comic_vine_id' => $charData['id']],
                        ['name' => $charData['name'] ?? null]
                    );
                    $issue->characters()->syncWithoutDetaching($character->id);
                }
            }

            // 5. Attach people with role
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

            $imported++;
        }

        return $imported;
    }

    public function importIssue(int $comicVineId)
    {
        $response = $this->comicVine->getIssue($comicVineId);
        $data     = $response['results'] ?? null;

        if (!$data) return null;

        // 1. Resolve volume first
        $volumeId = null;
        if (!empty($data['volume'])) {
            $volume   = $this->importVolume($data['volume']['id']);
            $volumeId = $volume?->id;
        }

        // 2. Save issue
        $issue = Issue::updateOrCreate(
            ['comic_vine_id' => $data['id']],
            [
                'name'         => $data['name'] ?? null,
                'issue_number' => $data['issue_number'] ?? null,
                'description'  => $data['description'] ?? null,
                'image'        => $data['image']['original_url'] ?? null,
                'cover_date'   => $data['cover_date'] ?? null,
                'volume_id'    => $volumeId,
            ]
        );

        // 3. Attach characters
        if (!empty($data['character_credits'])) {
            foreach ($data['character_credits'] as $charData) {
                $character = Character::firstOrCreate(
                    ['comic_vine_id' => $charData['id']],
                    ['name' => $charData['name'] ?? null]
                );
                $issue->characters()->syncWithoutDetaching($character->id);
            }
        }

        // 4. Attach people with role
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
}