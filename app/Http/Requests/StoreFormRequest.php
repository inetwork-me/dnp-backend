<?php

// app/Http/Requests/StoreFormRequest.php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreFormRequest extends FormRequest
{
    public function rules()
    {
        return [
            'label' => ['required', 'array', function ($attr, $val, $fail) {
                if (!array_filter($val, fn ($v) => trim($v) !== '')) {
                    $fail('At least one language must have a label.');
                }
            }],
            'slug' => 'required|alpha_dash|unique:forms,slug',
            'settings' => 'nullable|array',
        ];
    }
}
