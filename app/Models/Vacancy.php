<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Vacancy extends Model
{
    protected $fillable = ['career_id', 'title', 'description', 'type', 'location', 'arrangement', 'salary_min', 'salary_max', 'qualifications', 'skills', 'closing_date', 'application_url', 'status', 'qualification_level'];

    protected function casts(): array
    {
        return ['skills' => 'array', 'closing_date' => 'date:Y-m-d'];
    }

    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function scopeOpen(Builder $query): void
    {
        $query->where('status', 'published')->whereDate('closing_date', '>=', now('Asia/Kuala_Lumpur')->toDateString())
            ->whereHas('company', fn ($company) => $company->where('is_approved', true));
    }
}
