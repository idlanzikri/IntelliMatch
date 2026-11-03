<?php

namespace App\Services;

use App\Models\StudentProfile;
use App\Models\Vacancy;

class ApplicantMatch
{
    public function score(?StudentProfile $profile, Vacancy $vacancy): array
    {
        $normalize = fn ($values) => array_unique(array_map(fn ($s) => mb_strtolower(trim($s)), $values));
        $required = $normalize($vacancy->skills ?? []);
        $owned = $normalize($profile?->skills ?? []);
        $matched = array_values(array_intersect($required, $owned));

        $skills = count($required) ? count($matched) / count($required) * 100 : 0;
        $levels = ['certificate' => 1, 'diploma' => 2, 'degree' => 3];
        $known = isset($levels[$profile?->qualification_level ?? ''], $levels[$vacancy->qualification_level ?? '']);
        $qualification = $known && $levels[$profile->qualification_level] >= $levels[$vacancy->qualification_level] ? 100 : 0;

        return [
            'score' => (int) round($skills * .7 + $qualification * .3),
            'skills_score' => (int) round($skills),
            'qualification_score' => $known ? $qualification : null,
            'matched' => $matched,
            'missing' => array_values(array_diff($required, $owned))
        ];
    }
}
