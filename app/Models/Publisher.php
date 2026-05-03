<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Publisher extends Model
{
    use HasFactory;

    protected $fillable = [
        'comic_vine_id',
        'name',
        'description',
        'logo_url',
        'location_city',
        'location_state',
        'location_country',
        'aliases',
    ];

    protected $casts = [
        'aliases' => 'array',
    ];

    public function volumes()
    {
        return $this->hasMany(Volume::class);
    }
}