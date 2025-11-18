<?php

namespace App\Http\Resources\V2;

use Illuminate\Http\Resources\Json\ResourceCollection;

class UserCollection extends ResourceCollection
{
    /**
     * The resource that this resource collects.
     *
     * @var string
     */
    public $collects = UserResource::class;

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
