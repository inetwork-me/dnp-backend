<?php
// app/Http/Resources/FormSubmissionResource.php

namespace App\Http\Resources\V2;

use Illuminate\Http\Resources\Json\JsonResource;

class FormSubmissionResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'         => $this->id,
            'form_id'    => $this->form_id,
            'data'       => $this->data,
            'created_at' => $this->created_at->toDateTimeString(),
        ];
    }
}
