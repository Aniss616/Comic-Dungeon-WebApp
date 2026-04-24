<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    protected $fillable = [
        'username',
        'email',
        'password_hash'
    ];

    protected $hidden = [
        'password_hash'
    ];

    public function reads()
    {
        return $this->belongsToMany(Issue::class, 'user_reads')
                    ->withPivot('read_date');
    }

    public function favourites()
    {
        return $this->belongsToMany(Issue::class, 'user_favourites')
                    ->withPivot('favourite_date');
    }

    public function favouriteCharacters()
    {
        return $this->favourites
                    ->flatMap->characters
                    ->unique('id');
    }
}