<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Institution extends Model
{
    protected $fillable = ['name', 'code', 'agency_id', 'address', 'state', 'latitude', 'longitude'];

    protected function casts(): array
    {
        return ['latitude' => 'float', 'longitude' => 'float'];
    }

    public function agency(): BelongsTo
    {
        return $this->belongsTo(Agency::class);
    }

    public function courses(): HasMany
    {
        return $this->hasMany(InstitutionCourse::class);
    }
}
