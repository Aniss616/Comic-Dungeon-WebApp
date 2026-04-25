<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use HasFactory;
class Issue extends Model
{

    protected $fillable = [
        'comic_vine_id',
        'name',
        'issue_number',
        'description',
        'image',
        'volume_id',
    ];

    public function volume()
    {
        return $this->belongsTo(Volume::class);
    }

    public function people()
    {
        return $this->belongsToMany(Person::class, 'issue_people')
            ->withPivot('role')
            ->withTimestamps();
    }

    public function characters()
    {
        return $this->belongsToMany(Character::class, 'issue_characters')
            ->withTimestamps();
    }
}