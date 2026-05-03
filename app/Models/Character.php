<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Character extends Model
{
    use HasFactory;

    protected $fillable = [
        'comic_vine_id',
        'name',
        'real_name',
        'description',
        'aliases',
        'image',
        'birth',
        'gender',
        'origin',
        'publisher',
        'powers',
        'teams',
        'character_friends',
        'character_enemies',
        'first_appeared_in_issue',
    ];

    protected $casts = [
        'aliases'               => 'array',
        'powers'                => 'array',
        'teams'                 => 'array',
        'character_friends'     => 'array',
        'character_enemies'     => 'array',
        'first_appeared_in_issue' => 'array',
    ];

    public function getGenderLabelAttribute(): string
    {
        return match($this->gender) {
            1 => 'Male',
            2 => 'Female',
            3 => 'Other',
            default => 'Unknown',
        };
    }

    public function issues()
    {
        return $this->belongsToMany(Issue::class, 'issue_characters')
            ->withTimestamps();
    }

    public function favouritedBy()
    {
        return $this->belongsToMany(User::class, 'user_favourite_characters')
            ->withTimestamps();
    }
}