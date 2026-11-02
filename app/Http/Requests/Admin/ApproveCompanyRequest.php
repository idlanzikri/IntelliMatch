<?php

namespace App\Http\Requests\Admin;

class ApproveCompanyRequest extends IndexAdminRequest
{
    public function rules(): array
    {
        return ['is_approved' => ['required', 'boolean']];
    }
}
