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
            'fields' => 'sometimes|array',
            'fields.*.label' => 'required|array',
            'fields.*.name' => 'required|string',
            'fields.*.type' => 'required|string',
            'fields.*.required' => 'nullable|boolean',
            'fields.*.supports_multilang' => 'nullable|boolean',
            'fields.*.multiple' => 'nullable|boolean',
            'fields.*.conditional_logic' => 'nullable|array',
            'fields.*.options' => 'nullable|array',
            'fields.*.validation' => 'nullable|array',
            'fields.*.order' => 'nullable|integer',
        ];
    }
}
