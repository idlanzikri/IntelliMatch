<?php

namespace App\Http\Controllers\Institution;

use App\Actions\Institution\ShowStudentAction;
use App\Actions\Institution\ShowWorkspaceAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Institution\ViewInstitutionRequest;
use Inertia\Inertia;
use Inertia\Response;

class WorkspaceController extends Controller
{
    public function index(ViewInstitutionRequest $request, ShowWorkspaceAction $action): Response
    {
        return Inertia::render('institution/Overview', $action->execute($request));
    }

    public function show(ViewInstitutionRequest $request, ShowStudentAction $action, int $student): Response
    {
        return Inertia::render('institution/Student', $action->execute($request, $student));
    }
}
