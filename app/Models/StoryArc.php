<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StoryArc extends Model
{
    protected $fillable = ['comic_vine_id', 'name'];

    public function issues()
    {
        return $this->belongsToMany(Issue::class, 'issue_story_arc')
            ->withTimestamps()
            ->orderBy('cover_date')
            ->orderBy('issue_number');
    }
}