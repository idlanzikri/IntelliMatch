<?php

namespace App\Actions\Admin;

use App\Http\Requests\Admin\IndexAdminRequest;
use App\Models\Career;
use App\Models\StudentProfile;
use App\Models\User;

class ShowOverviewAction
{
    public function execute(IndexAdminRequest $request): array
    {
        return [
            'stats' => [
                'students' => User::where('role', 'student')->count(),
                'profiles' => StudentProfile::whereIn('user_id', User::where('role', 'student')->select('id'))->count(),
                'careers' => Career::count(),
                'activeCareers' => Career::where('is_active', true)->count(),
            ],
            
            'recentStudents' => User::where('role', 'student')->latest()->limit(5)->get(['id', 'name', 'email', 'created_at']),
        ];
    }
}
