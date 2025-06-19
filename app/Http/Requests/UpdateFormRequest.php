<?php

// app/Http/Requests/UpdateFormRequest.php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateFormRequest extends FormRequest
{
    public function rules()
    {
        return [
            'label' => ['sometimes', 'array', function ($a, $v, $f) {
                if (!array_filter($v, fn ($x) => trim($x) !== '')) {
                    $f('At least one language label is required.');
                }
            }],
            'slug' => [
                'sometimes', 'required', 'alpha_dash',
                Rule::unique('forms', 'slug')->ignore($this->route('form')->id)
            ],
            'settings' => 'nullable|array',
        ];
    }
}
