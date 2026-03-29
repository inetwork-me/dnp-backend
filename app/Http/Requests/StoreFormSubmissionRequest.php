<?php
// app/Http/Requests/StoreFormSubmissionRequest.php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreFormSubmissionRequest extends FormRequest
{
    public function authorize(): bool
    {
        // public endpoint—allow all
        return true;
    }

    public function rules(): array
    {
        return [
            'data' => ['required', 'array'],
            // optionally, enforce individual field rules, e.g.:
            // 'data.*' => ['string', 'max:1000'],
        ];
    }
}
