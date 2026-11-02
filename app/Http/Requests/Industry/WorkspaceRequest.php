<?php

namespace App\Http\Requests\Industry;

use Illuminate\Foundation\Http\FormRequest;

class WorkspaceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === 'industry';
    }

    public function rules(): array
    {
        return ['page' => ['nullable', 'integer', 'min:1']];
    }
}
