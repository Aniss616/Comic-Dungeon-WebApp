<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Publisher extends Model
{
    protected $fillable = [
        'comic_vine_id',
        'name',
        'description',
        'logo_url',
    ];
}