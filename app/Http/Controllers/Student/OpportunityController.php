<?php

namespace App\Http\Controllers\Student;

use App\Actions\Career\IndexOpportunitiesAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Career\ViewCareerRequest;

class OpportunityController extends Controller
{
    public function index(ViewCareerRequest $request, IndexOpportunitiesAction $action)
    {
        return inertia('student/Opportunities', $action->execute($request));
    }
}
