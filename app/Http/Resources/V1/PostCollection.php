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
                    'title' => $data->title,
                    'slug' => $data->slug,
                    'description' => $data->description,
                    'content' => $data->content,
                    'blocks' => $this->transformBlocks($data->blocks),
                    'featured_image' => $data->featured_image,
                    'created_at' => $data->created_at,
                    'author' => $data->author,
                    'category' => $data->category,
                    'fields' => $data->fields,
                    'seo' => $data->seo
                ];
            })
        ];
    }

    /**
     * Transform blocks to match WebsitePostResource format
     */
    protected function transformBlocks($rawBlocks): array
    {
        if (is_string($rawBlocks)) {
            $rawBlocks = json_decode($rawBlocks, true) ?: [];
        }
        $rawBlocks = is_array($rawBlocks) ? $rawBlocks : [];

        $blocks = [];
        foreach ($rawBlocks as $block) {
            if (!is_array($block)) {
                continue;
            }

            $fields = [];
            if (!empty($block['fields']) && is_array($block['fields'])) {
                foreach ($block['fields'] as $field) {
                    if (isset($field['name'])) {
                        $fields[$field['name']] = $field['value'] ?? null;
                    }
                }
            }

            $blocks[] = [
                'blockId' => $block['blockId'] ?? null,
                'name' => $block['name'] ?? null,
                'fields' => $fields,
            ];
        }

        return $blocks;
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