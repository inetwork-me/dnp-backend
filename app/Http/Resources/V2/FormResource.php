<?php
// app/Http/Resources/FormResource.php

namespace App\Http\Resources\V2;

use Illuminate\Http\Resources\Json\JsonResource;

class FormResource extends JsonResource
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
            'id'       => $this->id,
            'label' => $this->label,
            'slug'     => $this->slug,
            'settings' => $this->settings,
            'fields'   => FormFieldResource::collection($this->whenLoaded('fields')),
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at->toDateTimeString(),
        ];
    }
}
