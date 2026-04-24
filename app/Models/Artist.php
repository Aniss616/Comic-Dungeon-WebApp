<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Artist extends Model
{
    protected $fillable = [
        'name',
        'bio'
    ];

    public function issues()
    {
        return $this->belongsToMany(Issue::class, 'issue_artist');
    }
}
