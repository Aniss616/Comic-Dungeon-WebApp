<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Character extends Model
{
    protected $fillable = [
        'name',
        'alias',
        'abilities',
        'avatar_url',
        'universe'
    ];

    public function issues()
    {
        return $this->belongsToMany(Issue::class, 'issue_character');
    }
}
