<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Issue extends Model
{
    use HasFactory;

    protected $fillable = [
        'comic_vine_id',
        'name',
        'issue_number',
        'description',
        'image',
        'cover_date',
        'store_date',
        'teams',
        'locations',
        'story_arc_credits',
        'site_detail_url',
        'volume_id',
    ];

    protected $casts = [
        'teams'             => 'array',
        'locations'         => 'array',
        'story_arc_credits' => 'array',
    ];

    public function volume()
    {
        return $this->belongsTo(Volume::class);
    }

    public function people()
    {
        return $this->belongsToMany(Person::class, 'issue_people', 'issue_id', 'people_id')
            ->withPivot('role')
            ->withTimestamps();
    }

    public function characters()
    {
        return $this->belongsToMany(Character::class, 'issue_characters')
            ->withTimestamps();
    }

    public function storyArcs()
    {
        return $this->belongsToMany(StoryArc::class, 'issue_story_arc')->withTimestamps();
    }

    public function teamRecords()
    {
    return $this->belongsToMany(Team::class, 'issue_team')->withTimestamps();
    }

    
    public function locationRecords()
    {
    return $this->belongsToMany(Location::class, 'issue_location')->withTimestamps();
    }
}