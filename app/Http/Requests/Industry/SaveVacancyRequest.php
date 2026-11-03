<?php

namespace App\Http\Requests\Industry;

use Illuminate\Validation\Rule;

class SaveVacancyRequest extends WorkspaceRequest
{
    public function rules(): array
    {
        return [
            'qualification_level' => ['nullable', Rule::in(['certificate', 'diploma', 'degree'])],
            'title' => ['required', 'string', 'max:180'], 'description' => ['required', 'string', 'max:5000'],
            'type' => ['required', Rule::in(['full-time', 'part-time', 'contract', 'internship'])],
            'arrangement' => ['required', Rule::in(['on-site', 'hybrid', 'remote'])],
            'location' => ['required', 'string', 'max:180'], 'career_id' => ['nullable', 'integer', 'exists:careers,id'],
            'salary_min' => ['nullable', 'integer', 'min:0', 'max:1000000'], 'salary_max' => ['nullable', 'integer', 'min:0', 'max:1000000', ...($this->filled('salary_min') ? ['gte:salary_min'] : [])],
            'qualifications' => ['nullable', 'string', 'max:2000'], 'skills' => ['required', 'array', 'min:1', 'max:30'], 'skills.*' => ['required', 'string', 'max:100', 'distinct:ignore_case'],
            'closing_date' => ['required', 'date_format:Y-m-d'], 'application_url' => ['nullable', 'url:http,https', 'max:1000'],
            'status' => ['required', Rule::in(['draft', 'published', 'closed', 'archived'])],
        ];
    }
}
