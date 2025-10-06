<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Resources\Json\ResourceCollection;

class ProductMiniCollection extends ResourceCollection
{
    public function toArray($request)
    {
        return [
            'data' => $this->collection->map(function ($data) use ($request) {
                $wholesale_product =
                    ($data->wholesale_product == 1) ? true : false;

                $avgRating = $data->avg_rating ?? (float) $data->rating;

                // Check if product is in user's cart
                $isInCart = false;
                if ($request->user()) {
                    $isInCart = $request->user()->cart()
                        ->whereHas('items', function ($query) use ($data) {
                            $query->where('product_id', $data->id);
                        })
                        ->exists();
                }

                // Check if product is in user's wishlist
                $isInWishlist = false;
                if ($request->user()) {
                    $isInWishlist = $request->user()->wishlists()
                        ->where('product_id', $data->id)
                        ->exists();
                }

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
                    'main_price' => $data->unit_price,
                    'discounted_price' => home_discounted_base_price($data),
                    'rating' => (float) $data->rating,
                    'sales' => (int) $data->num_of_sale,
                    'current_stock' =>  $data->current_stock,
                    'is_top_selling' =>  $data->is_top_selling,
                    'desc' =>  $data->desc,
                    'type' =>  $data->type,
                    'category'  => get_single_category($data->category_id),
                    'avg_rating' => round((float) $avgRating, 1),
                    'package_details' => $data->packageDetails,
                    'is_subscription' => $data->is_subscription,
                    'loyalty_points' => $data->getEffectiveLoyaltyPoints(),
                    'returns' => $data->returns,
                    'requires_branch_selection' => $data->requires_branch_selection ?? false,
                    'is_in_cart' => $isInCart,
                    'is_in_wishlist' => $isInWishlist

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
