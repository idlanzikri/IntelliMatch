<?php

namespace App\Actions\Admin;

use App\Http\Requests\Admin\IndexAdminRequest;
use App\Models\StudentProfile;
use App\Models\User;

class IndexStudentsAction
{
    public function execute(IndexAdminRequest $request): array
    {
        $search = trim((string) $request->validated('search', ''));
        $students = User::where('role', 'student')
            ->when($search !== '', fn ($query) => $query->where(fn ($query) => $query
                ->whereRaw('LOWER(name) LIKE ?', ['%'.mb_strtolower($search).'%'])
                ->orWhereRaw('LOWER(email) LIKE ?', ['%'.mb_strtolower($search).'%'])))
            ->latest()->paginate(15, ['id', 'name', 'email', 'email_verified_at', 'created_at'])->withQueryString();
        $profiles = StudentProfile::whereIn('user_id', $students->pluck('id'))->get()->keyBy('user_id');
        $students->through(fn ($student) => [
            'id' => $student->id, 'name' => $student->name, 'email' => $student->email,
            'verified' => $student->email_verified_at !== null,
            'programme' => $profiles->get($student->id)?->programme,
            'institution' => $profiles->get($student->id)?->institution,
            'skillsCount' => count($profiles->get($student->id)?->skills ?? []),
        ]);

        return ['students' => $students, 'filters' => ['search' => $search]];
    }
}
