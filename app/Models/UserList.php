<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserList extends Model
{
    protected $fillable = ['user_id', 'name'];

    public function user() { return $this->belongsTo(User::class); }

    public function issues() {
        return $this->belongsToMany(Issue::class, 'user_list_issues')
            ->withPivot('sort_order')
            ->orderBy('sort_order')
            ->withTimestamps();
    }
}