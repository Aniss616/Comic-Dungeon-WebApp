<?php

namespace App\Services;

use App\Models\Character;

class CharacterService
{
    /**
     * Get a random character from database
     */
    public function random()
    {
        $character = Character::inRandomOrder()->first();

        if (!$character) {
            return null;
        }

        return [
            'id' => $character->id,
            'comic_vine_id' => $character->comic_vine_id,
            'name' => $character->name,
            'aliases' => $character->aliases ?? [],
            'description' => $character->description,
            'image' => $character->image,

            // placeholders for next features
            'first_appearance' => null,
            'best_start_comic' => null,
            'reading_path' => [],
        ];
    }
}