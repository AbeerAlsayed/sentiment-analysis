<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Topic extends Model
{
    protected $fillable = ['name', 'image'];

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

}
