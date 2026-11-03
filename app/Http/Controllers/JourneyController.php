<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\Career;
use App\Models\Company;
use App\Models\StudentProfile;
use App\Models\Vacancy;
use App\Services\ApplicantMatch;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class JourneyController extends Controller
{
    private function student(Request $request): void
    {
        abort_unless($request->user()->role === 'student', 403);
    }

    public function apply(Request $request, Vacancy $vacancy)
    {
        $this->student($request);
        abort_unless(Vacancy::open()->whereKey($vacancy->id)->exists(), 404);

        if (! StudentProfile::where('user_id', $request->user()->id)->exists()) {
            return redirect('/student/profile')->withErrors(['qualification_level' => 'Complete and save your career profile before applying.']);
        }

        $application = Application::firstOrNew(['user_id' => $request->user()->id, 'vacancy_id' => $vacancy->id]);
        $application->user_id = $request->user()->id;
        $application->vacancy_id = $vacancy->id;
        if (! $application->exists) {
            $application->status = 'submitted';
            $application->save();
        }

        return back();
    }

    public function applications(Request $request)
    {
        $this->student($request);

        return Inertia::render('student/Applications', ['applications' => Application::where('user_id', $request->user()->id)->with('vacancy.company')->latest()->paginate(10)]);
    }

    private function owned(Request $request, Vacancy $vacancy): void
    {
        abort_unless($request->user()->role === 'industry' && Company::whereKey($vacancy->company_id)->where('user_id', $request->user()->id)->exists(), 404);
    }

    public function applicants(Request $request, Vacancy $vacancy, ApplicantMatch $matcher)
    {
        $this->owned($request, $vacancy);
        $applications = Application::where('vacancy_id', $vacancy->id)->with('user:id,name,institution_id')->get();
        $profiles = StudentProfile::whereIn('user_id', $applications->pluck('user_id'))->get()->keyBy('user_id');

        $rows = $applications->map(function ($application) use ($profiles, $vacancy, $matcher) {
            $profile = $profiles->get($application->user_id);

            return [
                'id' => $application->id, 
                'name' => $application->user->name, 
                'profile' => $profile, 
                'status' => $application->status, 
                'date' => $application->created_at->toDateString(), 
                'match' => $matcher->score($profile, $vacancy)
            ];
        })->sortByDesc(fn ($row) => $row['match']['score'])->values();

        return Inertia::render('industry/Applicants', ['vacancy' => $vacancy, 'applicants' => $rows]);
    }

    public function status(Request $request, Vacancy $vacancy, Application $application)
    {
        $this->owned($request, $vacancy);
        abort_unless($application->vacancy_id === $vacancy->id, 404);
        $application->update($request->validate(['status' => ['required', Rule::in(['submitted', 'shortlisted', 'rejected'])]]));

        return back();
    }

    public function paths(Request $request)
    {
        $this->student($request);
        $profile = StudentProfile::where('user_id', $request->user()->id)->first();
        $careers = Career::where('is_active', true)->get()->map(function ($career) use ($profile) {
            $career->setAttribute('missing', array_values(array_diff($career->skills, $profile?->skills ?? [])));

            return $career;
        });

        return Inertia::render('student/Paths', [
            'profile' => $profile, 
            'careers' => $careers, 
            'vacancies' => $profile?->target_career_id ? Vacancy::open()->with('company')->where('career_id', $profile->target_career_id)->latest()->limit(5)->get() : []
        ]);
    }

    public function plan(Request $request)
    {
        $this->student($request);

        $data = $request->validate([
            'target_career_id' => ['required', Rule::exists('careers', 'id')->where('is_active', true)], 
            'completed_steps' => ['present', 'array', 'max:40'], 
            'completed_steps.*' => ['string', 'max:100', 'distinct']
        ]);

        $career = Career::findOrFail($data['target_career_id']);
        abort_if(array_diff($data['completed_steps'], array_merge($career->skills, ['project', 'qualification'])), 422);
        
        $profile = StudentProfile::firstOrNew(['user_id' => $request->user()->id]);
        $profile->user_id = $request->user()->id;
        $profile->target_career_id = $career->id;
        $profile->completed_steps = $data['completed_steps'];
        $profile->save();

        return back();
    }
}

