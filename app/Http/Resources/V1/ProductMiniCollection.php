<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Resources\Json\ResourceCollection;

class ProductMiniCollection extends ResourceCollection
{
    public function toArray($request)
    {
        return [
            'data' => $this->collection->map(function ($data) {
                $wholesale_product =
                    ($data->wholesale_product == 1) ? true : false;

                $avgRating = $data->reviews_rating_avg ?? (float) $data->rating;

                return [
                    'id' => $data->id,
                    'slug' => $data->slug,
                    'name' => $data->getTranslation('name'),
                    'thumbnail' => $data->thumbnail,
                    'label' => $data->label,
                    // 'thumbnail_image' => uploaded_asset($data->thumbnail_img),
                    'has_discount' => home_base_price($data, false) != home_discounted_base_price($data, false),
                    'discount' => discount_in_percentage($data),
                    'stroked_price' => home_base_price($data),
                    'main_price' => home_discounted_base_price($data),
                    'rating' => (float) $data->rating,
                    'sales' => (int) $data->num_of_sale,
                    'current_stock' =>  $data->current_stock,
                    'is_top_selling' =>  $data->is_top_selling,
                    'type' =>  $data->type,
                    'category'  => get_single_category($data->category_id),
                    'avg_rating' => round((float) $avgRating, 1),
                    'package_details' => $data->packageDetails,




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
