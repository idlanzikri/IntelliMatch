<?php

namespace App\Actions\Admin;

use App\Http\Requests\Admin\ApproveCompanyRequest;
use App\Models\Company;

class ApproveCompanyAction
{
    public function execute(ApproveCompanyRequest $request, Company $company): void
    {
        $company->is_approved = $request->boolean('is_approved');
        $company->save();
    }
}
