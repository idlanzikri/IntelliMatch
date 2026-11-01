<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class IndexAdminRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user()?->is_admin;
    }

    public function rules(): array
    {
        return ['search' => ['nullable', 'string', 'max:180'], 'page' => ['nullable', 'integer', 'min:1']];
    }
}
