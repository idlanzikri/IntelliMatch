<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $fillable = ['name', 'industry', 'location', 'description', 'website'];

    protected function casts(): array
    {
        return ['is_approved' => 'boolean', 'latitude' => 'float', 'longitude' => 'float', 'location_is_approximate' => 'boolean'];
    }
}
