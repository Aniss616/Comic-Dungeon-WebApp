<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    protected $fillable = [
        'username',
        'email',
        'password_hash',
        'is_admin',
    ];

    protected $hidden = [
        'password_hash',
    ];

    public function getAuthPassword()
    {
        return $this->password_hash;
    }

    public function reads()
    {
        return $this->belongsToMany(Issue::class, 'user_reads')
                    ->withPivot('read_date')
                    ->withTimestamps();
    }

    public function favourites()
    {
        return $this->belongsToMany(Issue::class, 'user_favourites')
                    ->withPivot('favourite_date')
                    ->withTimestamps();
    }

    public function favouriteCharacters()
    {
        return $this->belongsToMany(Character::class, 'user_favourite_characters')
                    ->withTimestamps();
    }
}