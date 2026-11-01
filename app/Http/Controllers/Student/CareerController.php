<?php

namespace App\Http\Controllers\Student;

use App\Actions\Career\ShowDashboardAction;
use App\Actions\Career\UpdateProfileAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Career\UpdateProfileRequest;
use App\Http\Requests\Career\ViewCareerRequest;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class CareerController extends Controller
{
    public function index(ViewCareerRequest $request, ShowDashboardAction $action): Response
    {
        return Inertia::render('Dashboard', $action->execute($request));
    }

    public function edit(ViewCareerRequest $request, ShowDashboardAction $action): Response
    {
        return Inertia::render('student/Profile', $action->execute($request));
    }

    public function update(UpdateProfileRequest $request, UpdateProfileAction $action): RedirectResponse
    {
        $action->execute($request);

        return to_route('dashboard');
    }
}
