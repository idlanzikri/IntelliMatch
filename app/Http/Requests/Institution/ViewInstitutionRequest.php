<?php

namespace App\Http\Requests\Institution;

use Illuminate\Foundation\Http\FormRequest;

class ViewInstitutionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === 'institution' && $this->user()->institution_id !== null;
    }

    public function rules(): array
    {
        return ['search' => ['nullable', 'string', 'max:180'], 'page' => ['nullable', 'integer', 'min:1']];
    }
}
