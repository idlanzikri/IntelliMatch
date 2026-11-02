<?php

namespace App\Actions\Admin;

use App\Http\Requests\Admin\IndexAdminRequest;
use App\Models\Company;

class IndexCompaniesAction
{
    public function execute(IndexAdminRequest $request): array
    {
        return ['companies' => Company::orderBy('is_approved')->latest()->paginate(15)];
    }
}
