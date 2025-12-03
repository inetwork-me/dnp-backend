<?php
// app/Http/Resources/V1/WebsitePostResource.php

namespace App\Http\Resources\V1;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Product;

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

        $type   = $this->postType;           // eager-load in controller: with('postType')
        $defs   = $type->fields ?? [];       // array of field‐defs
        $values = $this->fields   ?? [];     // from Post::$casts

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
                        $fieldValue = $field['value'] ?? null;

                        // Check if this is a relation field with products
                        $isProductRelation = isset($field['relation_objectType']) && $field['relation_objectType'] === 'products';

                        // Expand product relations to full product data
                        if ($isProductRelation && $fieldValue) {
                            $fieldValue = $this->expandProductData($fieldValue);
                        }

                        // Handle repeater fields that may contain product relations
                        if ($field['type'] === 'repeater' && is_array($fieldValue)) {
                            $fieldValue = $this->expandRepeaterProducts($fieldValue, $field['fields'] ?? []);
                        }

                        $fields[$field['name']] = $fieldValue;
                    }
                }
            }

            $blocks[] = [
                'blockId' => $block['blockId'] ?? null,
                'name'    => $block['name']    ?? null,
                // 'label'   => $block['label']   ?? null,
                // 'icon'    => $block['icon']    ?? null,
                'fields'  => $fields,
            ];
        }

        // 3) Safely include author if loaded
        $author = null;
        if ($this->whenLoaded('author') && $this->author) {
            $author = [
                'id'    => $this->author->id,
                'name'  => $this->author->name,
                'email' => $this->author->email,
            ];
        }

        $category = null;
        if ($this->whenLoaded('category') && $this->category) {
            $category = [
                'id'   => $this->category->id,
                'name' => $this->category->name,  // array [en,ar]
                'slug' => $this->category->slug,
            ];
        }

        return [
            'id'             => $this->id,
            'slug'           => $this->slug,
            'title'          => $this->title,          // or localize here
            'content'        => $this->content,        // or localize here
            'featured_image' => $this->featured_image,
            'created_at' => $this->created_at,
            'seo' => $this->seo,
            'author'         => $author,
            'blocks'         => $blocks,
            'category' => $category,
            'fields' => $this->fields,
            'description' => $this->description,
            'postType' => $type,

        ];
    }

    /**
     * Expand a product ID to full product data
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

        return $this->formatProduct($product);
    }

    /**
     * Expand product relations within repeater fields
     */
    protected function expandRepeaterProducts(array $repeaterItems, array $fieldDefs): array
    {
        // Find which fields in the repeater are product relations from field definitions
        $productFieldNames = [];
        foreach ($fieldDefs as $fieldDef) {
            if (isset($fieldDef['relation_objectType']) && $fieldDef['relation_objectType'] === 'products') {
                $productFieldNames[] = $fieldDef['name'];
            }
        }

        // Collect all product IDs from the repeater items
        // Check both defined fields and scan for relation_objectType in item data
        $productIds = [];
        $fieldsToExpand = []; // Track which fields contain product IDs

        foreach ($repeaterItems as $itemIndex => $item) {
            // Check defined product fields
            foreach ($productFieldNames as $fieldName) {
                if (isset($item[$fieldName]['value']) && $item[$fieldName]['value']) {
                    $productIds[] = $item[$fieldName]['value'];
                    $fieldsToExpand[$fieldName] = true;
                } elseif (isset($item[$fieldName]) && is_numeric($item[$fieldName])) {
                    $productIds[] = $item[$fieldName];
                    $fieldsToExpand[$fieldName] = true;
                }
            }

            // Also scan item for any field with relation_objectType = products
            foreach ($item as $fieldName => $fieldData) {
                if (is_array($fieldData)) {
                    if (isset($fieldData['relation_objectType']) && $fieldData['relation_objectType'] === 'products') {
                        if (isset($fieldData['value']) && $fieldData['value']) {
                            $productIds[] = $fieldData['value'];
                            $fieldsToExpand[$fieldName] = true;
                        }
                    }
                }
            }
        }

        if (empty($productIds)) {
            return $repeaterItems;
        }

        // Fetch all products at once for efficiency
        $products = Product::with(['product_translations', 'main_category', 'brand'])
            ->whereIn('id', array_unique($productIds))
            ->get()
            ->keyBy('id');

        // Replace product IDs with full product data
        foreach ($repeaterItems as &$item) {
            foreach (array_keys($fieldsToExpand) as $fieldName) {
                $productId = null;

                if (isset($item[$fieldName]['value']) && $item[$fieldName]['value']) {
                    $productId = $item[$fieldName]['value'];
                } elseif (isset($item[$fieldName]) && is_numeric($item[$fieldName])) {
                    $productId = $item[$fieldName];
                }

                if ($productId && isset($products[$productId])) {
                    $item[$fieldName] = $this->formatProduct($products[$productId]);
                }
            }
        }

        return $repeaterItems;
    }

    /**
     * Format a product for the API response
     * Returns all product fields needed by frontend components
     */
    protected function formatProduct(Product $product): array
    {
        // Return full product data with all attributes and relationships
        $data = $product->toArray();

        // Add category and brand relationships if loaded
        if ($product->relationLoaded('main_category') && $product->main_category) {
            $data['category'] = [
                'id' => $product->main_category->id,
                'name' => $product->main_category->name,
                'slug' => $product->main_category->slug,
            ];
        }

        if ($product->relationLoaded('brand') && $product->brand) {
            $data['brand'] = [
                'id' => $product->brand->id,
                'name' => $product->brand->name,
            ];
        }

        return $data;
    }
}
