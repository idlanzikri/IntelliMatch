<?php

namespace App\Actions\Industry;

use App\Http\Requests\Industry\WorkspaceRequest;
use App\Models\Career;
use App\Models\Company;
use App\Models\Vacancy;

class ShowWorkspaceAction
{
    public function execute(WorkspaceRequest $request): array
    {
        $company = Company::where('user_id', $request->user()->id)->first();

        return [
            'company' => $company,
            'vacancies' => $company ? Vacancy::withCount('applications')->where('company_id', $company->id)->latest()->paginate(15) : null,
            'careers' => Career::where('is_active', true)->orderBy('title')->get(['id', 'title']),
        ];
    }
}
