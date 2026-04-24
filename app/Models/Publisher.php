<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Publisher extends Model
{
    protected $fillable = [
        'name',
        'logo_url',
        'country',
        'description'
    ];

    public function comics()
    {
        return $this->belongsToMany(Comic::class, 'comic_publisher');
    }
}
