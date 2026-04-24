<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReadingPath extends Model
{
    protected $fillable = [
        'title',
        'mood',
        'description',
        'difficulty'
    ];

    public function issues()
    {
        return $this->belongsToMany(Issue::class, 'issue_reading_path')
                    ->withPivot('order_position', 'start_here');
    }
}
