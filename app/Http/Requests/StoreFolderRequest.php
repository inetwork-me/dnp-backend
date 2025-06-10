<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreFolderRequest extends FormRequest
{
    public function rules()
    {
        return [
            'name'      => 'required|string|max:255',
            'parent_id' => 'nullable|exists:media_folders,id',
        ];
    }
}
