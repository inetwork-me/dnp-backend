<?php

namespace App\Http\Resources\V2;

use Illuminate\Http\Resources\Json\ResourceCollection;

class LanguageCollection extends ResourceCollection
{
    public function toArray($request)
    {
        // Let Laravel output "data", "links", and "meta" automatically:
        return parent::toArray($request);
    }

    public function with($request)
    {
        return [
            'success' => true,
            'status' => 200
        ];
    }
}
