<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Admin\IndexCareersAction;
use App\Actions\Admin\IndexStudentsAction;
use App\Actions\Admin\SaveCareerAction;
use App\Actions\Admin\ShowOverviewAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\IndexAdminRequest;
use App\Http\Requests\Admin\SaveCareerRequest;
use App\Models\Career;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class AdminController extends Controller
{
    public function index(IndexAdminRequest $request, ShowOverviewAction $action): Response
    {
        return Inertia::render('admin/Overview', $action->execute($request));
    }

    public function students(IndexAdminRequest $request, IndexStudentsAction $action): Response
    {
        return Inertia::render('admin/Students', $action->execute($request));
    }

    public function careers(IndexAdminRequest $request, IndexCareersAction $action): Response
    {
        return Inertia::render('admin/Careers', $action->execute($request));
    }

    public function store(SaveCareerRequest $request, SaveCareerAction $action): RedirectResponse
    {
        $action->execute($request);

        return to_route('admin.careers');
    }

    public function update(SaveCareerRequest $request, SaveCareerAction $action, Career $career): RedirectResponse
    {
        $action->execute($request, $career);

        return to_route('admin.careers');
    }
}
