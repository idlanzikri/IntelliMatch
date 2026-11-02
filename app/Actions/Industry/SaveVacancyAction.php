<?php

namespace App\Actions\Industry;

use App\Http\Requests\Industry\SaveVacancyRequest;
use App\Models\Company;
use App\Models\Vacancy;
use Illuminate\Validation\ValidationException;

class SaveVacancyAction
{
    public function execute(SaveVacancyRequest $request, ?int $id = null): void
    {
        $company = Company::where('user_id', $request->user()->id)->firstOrFail();

        $vacancy = $id ? Vacancy::where('company_id', $company->id)->findOrFail($id) : new Vacancy;

        $data = $request->validated();

        if ($data['status'] === 'published' && ! $company->is_approved) {
            throw ValidationException::withMessages(['status' => 'Your company must be approved before publishing.']);
        }

        if ($data['status'] === 'published' && $data['closing_date'] < now('Asia/Kuala_Lumpur')->toDateString()) {
            throw ValidationException::withMessages(['closing_date' => 'A published opening must have a current or future closing date.']);
        }
        
        $vacancy->company_id = $company->id;
        $vacancy->fill($data)->save();
    }
}
