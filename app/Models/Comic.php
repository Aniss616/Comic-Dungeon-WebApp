<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comic extends Model
{
    protected $fillable = [
        'title',
        'genre',
        'description',
        'cover_image'
    ];

    public function issues()
    {
        return $this->hasMany(Issue::class);
    }

    public function publishers()
    {
        return $this->belongsToMany(Publisher::class, 'comic_publisher');
    }
}
