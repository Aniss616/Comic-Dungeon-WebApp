<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class Character extends Model
{
    use HasFactory;
    protected $fillable = [
        'comic_vine_id',
        'name',
        'aliases',
        'description',
        'image',
    ];

    protected $casts = [
        'aliases' => 'array', // JSON → array automatically
    ];

    public function issues()
    {
        return $this->belongsToMany(Issue::class, 'issue_characters')
            ->withTimestamps();
    }
}
