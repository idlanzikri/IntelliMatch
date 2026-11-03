<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Agency extends Model
{
    protected $fillable = ['source_id', 'name'];

    public function institutions(): HasMany
    {
        return $this->hasMany(Institution::class);
    }
}
