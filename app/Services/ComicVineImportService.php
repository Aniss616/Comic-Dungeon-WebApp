<?php

namespace App\Services;

use App\Models\Character;

class ComicVineImportService
{
    protected ComicVineService $api;

    public function __construct(ComicVineService $api)
    {
        $this->api = $api;
    }

    /* ======================================================
        CHARACTERS
    ====================================================== */

    public function importCharacters(int $limit = 10, int $offset = 0)
    {
        $response = $this->api->getCharacters($limit, $offset);

        $results = $response['results'] ?? [];

        $imported = 0;

        foreach ($results as $data) {

            Character::updateOrCreate(
                [
                    'comic_vine_id' => $data['id'],
                ],
                [
                    'name' => $data['name'] ?? null,
                    'description' => $data['deck'] ?? null,
                    'aliases' => $this->parseAliases($data['aliases'] ?? null),
                    'image' => $data['image']['original_url'] ?? null,
                ]
            );

            $imported++;
        }

        return $imported;
    }

    public function importCharacter(int $comicVineId)
    {
        $response = $this->api->getCharacter($comicVineId);

        $data = $response['results'] ?? null;

        if (!$data) {
            return null;
        }

        return Character::updateOrCreate(
            [
                'comic_vine_id' => $data['id'],
            ],
            [
                'name' => $data['name'] ?? null,
                'description' => $data['deck'] ?? null,
                'aliases' => $this->parseAliases($data['aliases'] ?? null),
                'image' => $data['image']['original_url'] ?? null,
            ]
        );
    }

    /* ======================================================
        HELPERS
    ====================================================== */

    private function parseAliases(?string $aliases): array
    {
        if (!$aliases) {
            return [];
        }

        return array_values(array_filter(
            array_map('trim', explode("\n", $aliases))
        ));
    }
}