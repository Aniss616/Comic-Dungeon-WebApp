<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    protected $fillable = ['comic_vine_id', 'name', 'description', 'image'];

    public function issues()
    {
        return $this->belongsToMany(Issue::class, 'issue_location')
            ->withTimestamps()
            ->orderBy('cover_date')
            ->orderBy('issue_number');
    }
}