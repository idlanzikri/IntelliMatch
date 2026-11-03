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
        if (isset($data['latitude'], $data['longitude']) &&
            ((float) $data['latitude'] !== $vacancy->latitude || (float) $data['longitude'] !== $vacancy->longitude)) {
            $vacancy->location_is_approximate = false;
        }
        if (! $vacancy->exists && $data['arrangement'] !== 'remote' &&
            ! isset($data['latitude']) && ! isset($data['longitude']) &&
            $company->latitude !== null && $company->longitude !== null) {
            $data['latitude'] = $company->latitude;
            $data['longitude'] = $company->longitude;
            $vacancy->location_is_approximate = $company->location_is_approximate;
        }
        if ($data['arrangement'] === 'remote') {
            $vacancy->location_is_approximate = false;
            $data['latitude'] = null;
            $data['longitude'] = null;
        }
        if ($data['status'] === 'published' && $data['arrangement'] !== 'remote' && (! isset($data['latitude']) || ! isset($data['longitude']))) {
            throw ValidationException::withMessages(['latitude' => 'Select the actual workplace on the map before publishing.']);
        }
        $data['application_url'] = $data['application_url'] ?? '';

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
