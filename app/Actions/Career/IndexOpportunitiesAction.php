<?php

namespace App\Actions\Career;

use App\Http\Requests\Career\ViewCareerRequest;
use App\Models\Vacancy;

class IndexOpportunitiesAction
{
    public function execute(ViewCareerRequest $request): array
    {
        return ['vacancies' => Vacancy::open()->with('company:id,name,industry')->latest()->paginate(15)];
    }
}
