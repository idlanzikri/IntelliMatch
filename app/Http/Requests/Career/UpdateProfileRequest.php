<?php

namespace App\Http\Requests\Career;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        $skills = array_unique(array_merge(...array_column(config('careers'), 'skills')));

        return [
            'institution' => ['nullable', 'string', 'max:180'],
            'programme' => ['nullable', 'string', 'max:180'],
            'location' => ['nullable', 'string', 'max:180'],
            'interests' => ['nullable', 'string', 'max:2000'],
            'skills' => ['present', 'array', 'max:30'],
            'skills.*' => ['string', 'distinct', Rule::in($skills)],
        ];
    }
}
