<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InstitutionCourse extends Model
{
    protected $fillable = ['institution_id', 'source_id', 'name', 'source_url'];

    public function institution(): BelongsTo
    {
        return $this->belongsTo(Institution::class);
    }
}
