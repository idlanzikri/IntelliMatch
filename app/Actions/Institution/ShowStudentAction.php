<?php

namespace App\Actions\Institution;

use App\Http\Requests\Institution\ViewInstitutionRequest;
use App\Models\Career;
use App\Models\StudentProfile;
use App\Models\User;

class ShowStudentAction
{
    public function execute(ViewInstitutionRequest $request, int $student): array
    {
        $user = User::where('role', 'student')->where('institution_id', $request->user()->institution_id)->findOrFail($student);
        $profile = StudentProfile::where('user_id', $user->id)->first();
        $skills = $profile?->skills ?? [];
        
        $careers = Career::where('is_active', true)->get()->map(function ($career) use ($skills) {
            $data = $career->toArray();
            $data['matched'] = array_values(array_intersect($career->skills, $skills));
            $data['missing'] = array_values(array_diff($career->skills, $skills));
            $data['score'] = (int) round(count($data['matched']) / max(1, count($career->skills)) * 100);

            return $data;
        })->sortByDesc('score')->values();

        return ['student' => $user->only('id', 'name', 'email'), 'profile' => $profile, 'careers' => $careers];
    }
}
