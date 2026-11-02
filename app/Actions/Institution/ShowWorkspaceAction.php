<?php

namespace App\Actions\Institution;

use App\Http\Requests\Institution\ViewInstitutionRequest;
use App\Models\Career;
use App\Models\Institution;
use App\Models\StudentProfile;
use App\Models\User;

class ShowWorkspaceAction
{
    public function execute(ViewInstitutionRequest $request): array
    {
        $query = User::where('role', 'student')->where('institution_id', $request->user()->institution_id);

        $profiles = StudentProfile::whereIn('user_id', (clone $query)->select('id'))->get();
        $skills = $profiles->flatMap(fn ($profile) => $profile->skills ?? [])->countBy()->sortDesc()->take(6);

        $search = trim((string) $request->validated('search', ''));

        $students = (clone $query)->when($search !== '', fn ($query) => $query->where(fn ($query) => $query
            ->whereRaw('LOWER(name) LIKE ?', ['%'.mb_strtolower($search).'%'])
            ->orWhereRaw('LOWER(email) LIKE ?', ['%'.mb_strtolower($search).'%'])))
            ->latest()->paginate(15, ['id', 'name', 'email', 'email_verified_at'])->withQueryString();

        $profileMap = $profiles->keyBy('user_id');

        $students->through(fn ($student) => [
            'id' => $student->id, 'name' => $student->name, 'email' => $student->email,
            'programme' => $profileMap->get($student->id)?->programme,
            'skillsCount' => count($profileMap->get($student->id)?->skills ?? []),
        ]);

        return [
            'institution' => Institution::findOrFail($request->user()->institution_id)->only('name', 'code'),
            'stats' => [
                'students' => (clone $query)->count(), 
                'profiles' => $profiles->count(), 
                'withSkills' => $profiles->filter(fn ($profile) => count($profile->skills ?? []) > 0)->count(), 
                'careers' => Career::where('is_active', true)->count()
            ],
            'skills' => $skills->map(fn ($count, $name) => [
                'name' => $name, 
                'count' => $count
            ])->values(),
            'students' => $students, 
            'filters' => ['search' => $search],
        ];
    }
}
