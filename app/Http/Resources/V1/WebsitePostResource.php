<?php
// app/Http/Resources/V1/WebsitePostResource.php

namespace App\Http\Resources\V1;

use Illuminate\Http\Resources\Json\JsonResource;

class WebsitePostResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array<string,mixed>
     */
    public function toArray($request): array
    {
        // 1) Prepare raw blocks as an array
        $rawBlocks = $this->blocks;
        if (is_string($rawBlocks)) {
            $rawBlocks = json_decode($rawBlocks, true) ?: [];
        }
        $rawBlocks = is_array($rawBlocks) ? $rawBlocks : [];

        // 2) Transform each block
        $blocks = [];
        foreach ($rawBlocks as $block) {
            if (!is_array($block)) {
                continue;
            }

            // Build the simple name => value map
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
                // 'name'    => $block['name']    ?? null,
                // 'label'   => $block['label']   ?? null,
                // 'icon'    => $block['icon']    ?? null,
                'fields'  => $fields,
            ];
        }

        // 3) Safely include author if loaded
        $author = null;
        if ($this->relationLoaded('author') && $this->author) {
            $author = [
                'id'    => $this->author->id,
                'name'  => $this->author->name,
                'email' => $this->author->email,
            ];
        }

        return [
            'id'             => $this->id,
            'slug'           => $this->slug,
            'title'          => $this->title,          // or localize here
            'content'        => $this->content,        // or localize here
            'featured_image' => $this->featured_image,
            'author'         => $author,
            'blocks'         => $blocks,
        ];
    }
}
