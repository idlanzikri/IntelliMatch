<?php

namespace App\Actions\Industry;

use App\Http\Requests\Industry\SaveCompanyRequest;
use App\Models\Company;

class SaveCompanyAction
{
    public function execute(SaveCompanyRequest $request): void
    {
        $company = Company::firstOrNew(['user_id' => $request->user()->id]);
        $company->user_id = $request->user()->id;
        $company->fill($request->validated());

        if ($company->isDirty()) {
            $company->is_approved = false;
        }
        
        $company->save();
    }
}
