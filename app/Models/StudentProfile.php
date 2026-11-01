<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentProfile extends Model
{
    protected $fillable = ['institution', 'programme', 'location', 'skills', 'interests'];

    protected function casts(): array
    {
        return ['skills' => 'array'];
    }
}
