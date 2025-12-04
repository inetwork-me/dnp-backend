<?php

namespace App\Http\Resources\V1;

use App\Http\Resources\UserResource;
use App\Models\Product;
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
                        $fieldValue = $field['value'] ?? null;

                        // Expand product relations
                        $isProductRelation = isset($field['relation_objectType']) && $field['relation_objectType'] === 'products';
                        if ($isProductRelation && $fieldValue) {
                            $fieldValue = $this->expandProductData($fieldValue);
                        }

                        // Handle repeater fields
                        if (($field['type'] ?? '') === 'repeater' && is_array($fieldValue)) {
                            $fieldValue = $this->expandRepeaterProducts($fieldValue, $field['fields'] ?? []);
                        }

                        $fields[$field['name']] = $fieldValue;
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

    /**
     * Expand product ID to full product data
     */
    protected function expandProductData($productId): ?array
    {
        if (!$productId) {
            return null;
        }

        $product = Product::with(['product_translations', 'main_category', 'brand'])
            ->find($productId);

        if (!$product) {
            return null;
        }

        $data = $product->toArray();
        $data['main_price'] = $product->unit_price;

        return $data;
    }

    /**
     * Expand product relations within repeater fields
     */
    protected function expandRepeaterProducts(array $repeaterItems, array $fieldDefs): array
    {
        $productFieldNames = [];
        foreach ($fieldDefs as $fieldDef) {
            if (isset($fieldDef['relation_objectType']) && $fieldDef['relation_objectType'] === 'products') {
                $productFieldNames[] = $fieldDef['name'];
            }
        }

        $productIds = [];
        $fieldsToExpand = [];

        foreach ($repeaterItems as $item) {
            foreach ($productFieldNames as $fieldName) {
                if (isset($item[$fieldName]['value']) && $item[$fieldName]['value']) {
                    $productIds[] = $item[$fieldName]['value'];
                    $fieldsToExpand[$fieldName] = true;
                } elseif (isset($item[$fieldName]) && is_numeric($item[$fieldName])) {
                    $productIds[] = $item[$fieldName];
                    $fieldsToExpand[$fieldName] = true;
                }
            }

            // Scan for relation_objectType in item data
            foreach ($item as $fieldName => $fieldData) {
                if (is_array($fieldData) && isset($fieldData['relation_objectType']) && $fieldData['relation_objectType'] === 'products') {
                    if (isset($fieldData['value']) && $fieldData['value']) {
                        $productIds[] = $fieldData['value'];
                        $fieldsToExpand[$fieldName] = true;
                    }
                }
            }
        }

        if (empty($productIds)) {
            return $repeaterItems;
        }

        $products = Product::with(['product_translations', 'main_category', 'brand'])
            ->whereIn('id', array_unique($productIds))
            ->get()
            ->keyBy('id');

        foreach ($repeaterItems as &$item) {
            foreach (array_keys($fieldsToExpand) as $fieldName) {
                $productId = null;

                if (isset($item[$fieldName]['value']) && $item[$fieldName]['value']) {
                    $productId = $item[$fieldName]['value'];
                } elseif (isset($item[$fieldName]) && is_numeric($item[$fieldName])) {
                    $productId = $item[$fieldName];
                }

                if ($productId && isset($products[$productId])) {
                    $product = $products[$productId];
                    $data = $product->toArray();
                    $data['main_price'] = $product->unit_price;
                    $item[$fieldName] = $data;
                }
            }
        }

        return $repeaterItems;
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