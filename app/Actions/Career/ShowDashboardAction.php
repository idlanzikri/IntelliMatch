<?php

namespace App\Actions\Career;

use App\Http\Requests\Career\ViewCareerRequest;
use App\Models\StudentProfile;

class ShowDashboardAction
{
    public function execute(ViewCareerRequest $request): array
    {
        $profile = StudentProfile::where('user_id', $request->user()->id)->first();
        $skills = $profile?->skills ?? [];
        $careers = collect(config('careers'))->map(function (array $career) use ($skills) {
            $matched = array_values(array_intersect($career['skills'], $skills));
            $career['matched'] = $matched;
            $career['missing'] = array_values(array_diff($career['skills'], $skills));
            $career['score'] = (int) round(count($matched) / count($career['skills']) * 100);

            return $career;
        })->sortByDesc('score')->values()->all();

        return [
            'profile' => $profile,
            'careers' => $careers,
            'availableSkills' => array_values(array_unique(array_merge(...array_column(config('careers'), 'skills')))),
        ];
    }
}
