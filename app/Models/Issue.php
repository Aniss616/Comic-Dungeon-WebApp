<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Issue extends Model
{
    protected $fillable = [
        'comic_id',
        'issue_number',
        'cover_image',
        'release_date',
        'recommended_start'
    ];

    public function comic()
    {
        return $this->belongsTo(Comic::class);
    }

    public function authors()
    {
        return $this->belongsToMany(Author::class, 'issue_author');
    }

    public function artists()
    {
        return $this->belongsToMany(Artist::class, 'issue_artist');
    }

    public function characters()
    {
        return $this->belongsToMany(Character::class, 'issue_character');
    }

    public function readingPaths()
    {
        return $this->belongsToMany(ReadingPath::class, 'issue_reading_path');
    }

    public function readers()
    {
        return $this->belongsToMany(User::class, 'user_reads')
                    ->withPivot('read_date');
    }

    public function favouritedBy()
    {
        return $this->belongsToMany(User::class, 'user_favourites')
                    ->withPivot('favourite_date');
    }
}
