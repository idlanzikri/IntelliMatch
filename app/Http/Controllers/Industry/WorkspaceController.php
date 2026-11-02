<?php

namespace App\Http\Controllers\Industry;

use App\Actions\Industry\SaveCompanyAction;
use App\Actions\Industry\SaveVacancyAction;
use App\Actions\Industry\ShowWorkspaceAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Industry\SaveCompanyRequest;
use App\Http\Requests\Industry\SaveVacancyRequest;
use App\Http\Requests\Industry\WorkspaceRequest;

class WorkspaceController extends Controller
{
    public function index(WorkspaceRequest $request, ShowWorkspaceAction $action)
    {
        return inertia('industry/Workspace', $action->execute($request));
    }

    public function company(SaveCompanyRequest $request, SaveCompanyAction $action)
    {
        $action->execute($request);

        return to_route('industry.overview');
    }

    public function store(SaveVacancyRequest $request, SaveVacancyAction $action)
    {
        $action->execute($request);

        return to_route('industry.overview');
    }

    public function update(SaveVacancyRequest $request, SaveVacancyAction $action, int $vacancy)
    {
        $action->execute($request, $vacancy);

        return to_route('industry.overview');
    }
}
