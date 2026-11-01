<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Career extends Model
{
    protected $fillable = ['title', 'sector', 'description', 'skills', 'is_active'];

    protected function casts(): array
    {
        return ['skills' => 'array', 'is_active' => 'boolean'];
    }
}
