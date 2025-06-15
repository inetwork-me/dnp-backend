<?php

namespace App\Http\Resources\V1;

use App\Http\Resources\UserResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

class PostCollection extends ResourceCollection
{
    public function toArray($request)
    {
        return [
            'data' => $this->collection->map(function ($data) {
                return [
                    'id' => $data->id,
                    'id' => $data->id,
                    'title' => $data->title,
                    'slug' => $data->slug,
                    'description' => $data->description,
                    // 'content' => $data->content,
                    // 'blocks' => $data->blocks,
                    'featured_image' => $data->featured_image,
                    'created_at' => $data->created_at,
                    'author' => $data->author
                    // $data
                    // 'author'         => new UserResource($data->author),


                ];
            })
        ];
    }

    public function with($request)
    {
        return [
            'success' => true,
            'status' => 200
        ];
    }
}



// 'id' => $data->id,
// 'title' => $data->title,
// 'slug' => $data->slug,
// 'content' => $data->content,
// 'blocks' => $data->blocks,
// 'featured_image'=> $data->featured_image,
// 'created_at' => $data->created_at