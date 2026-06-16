<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    protected $fillable = ['comic_vine_id', 'name', 'description', 'image'];

    public function characters()
    {
        return $this->belongsToMany(Character::class, 'character_team')->withTimestamps();
    }

    public function issues()
    {
        return $this->belongsToMany(Issue::class, 'issue_team')
            ->withTimestamps()
            ->orderBy('cover_date')
            ->orderBy('issue_number');
    }
}