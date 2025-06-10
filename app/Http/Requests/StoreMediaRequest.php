<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMediaRequest extends FormRequest
{
    public function rules()
    {
        return [
            'files.*' => [
                'required',
                'file',
                'max:51200', // max 50 MB; change if you like
                // allow images, video, audio, pdf, docs, xls, ppt
                'mimetypes:' .
                    'image/*,' .
                    'video/*,' .
                    'audio/*,' .
                    'application/pdf,' .
                    'application/msword,' .
                    'application/vnd.openxmlformats-officedocument.wordprocessingml.document,' .
                    'application/vnd.ms-excel,' .
                    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,' .
                    'application/vnd.ms-powerpoint,' .
                    'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            ],
            'folder_id' => 'nullable|exists:media_folders,id',
            'tags' => 'nullable|array',
            'tags.*' => 'string',
        ];
    }
}
