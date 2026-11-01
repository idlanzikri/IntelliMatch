<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class SaveCareerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user()?->is_admin;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:180'],
            'sector' => ['required', 'string', 'max:180'],
            'description' => ['required', 'string', 'max:3000'],
            'skills' => ['required', 'array', 'min:1', 'max:30'],
            'skills.*' => ['required', 'string', 'max:100', 'distinct:ignore_case'],
            'is_active' => ['required', 'boolean'],
        ];
    }
}
