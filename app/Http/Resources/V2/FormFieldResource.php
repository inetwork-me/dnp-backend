<?php
// app/Http/Resources/FormFieldResource.php

namespace App\Http\Resources\V2;

use Illuminate\Http\Resources\Json\JsonResource;

class FormFieldResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'id'          => $this->id,
            'form_id'     => $this->form_id,
            'label'       => $this->label,       // array of locales
            'name'        => $this->name,
            'type'        => $this->type,
            'options'     => $this->options,     // array for selects/radios
            'validation'  => $this->validation,  // array of rules
            'order'       => $this->order,
            'created_at'  => $this->created_at->toDateTimeString(),
            'updated_at'  => $this->updated_at->toDateTimeString(),
        ];
    }
}
