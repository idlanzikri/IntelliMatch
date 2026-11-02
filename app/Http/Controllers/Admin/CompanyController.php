<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Admin\ApproveCompanyAction;
use App\Actions\Admin\IndexCompaniesAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ApproveCompanyRequest;
use App\Http\Requests\Admin\IndexAdminRequest;
use App\Models\Company;

class CompanyController extends Controller
{
    public function index(IndexAdminRequest $request, IndexCompaniesAction $action)
    {
        return inertia('admin/Companies', $action->execute($request));
    }

    public function update(ApproveCompanyRequest $request, ApproveCompanyAction $action, Company $company)
    {
        $action->execute($request, $company);

        return to_route('admin.companies');
    }
}
