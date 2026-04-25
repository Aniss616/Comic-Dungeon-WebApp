<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

    class Person extends Model
{
    use HasFactory;
    protected $fillable = [
        'comic_vine_id',
        'name',
        'description',
    ];

    public function issues()
    {
        return $this->belongsToMany(Issue::class, 'issue_people')
            ->withPivot('role')
            ->withTimestamps();
    }
}
