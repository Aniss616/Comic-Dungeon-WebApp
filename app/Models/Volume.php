<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Volume extends Model
{
    use HasFactory;
    protected $fillable = [
        'comic_vine_id',
        'name',
        'description',
        'cover_image',
        'publisher_id',
    ];

    public function publisher()
    {
        return $this->belongsTo(Publisher::class);
    }

    public function issues()
    {
        return $this->hasMany(Issue::class);
    }
}