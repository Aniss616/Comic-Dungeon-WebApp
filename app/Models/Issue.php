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
        'volume_id',
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
}