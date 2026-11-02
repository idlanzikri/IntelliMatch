<?php

namespace App\Http\Requests\Industry;

class SaveCompanyRequest extends WorkspaceRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:180'],
            'industry' => ['required', 'string', 'max:180'],
            'location' => ['required', 'string', 'max:180'],
            'description' => ['required', 'string', 'max:3000'],
            'website' => ['nullable', 'url:http,https', 'max:1000']
        ];
    }
}
