<?php

namespace App\Services;

use AizPackages\CombinationGenerate\Services\CombinationService;
use App\Models\Cart;
use App\Models\Category;
use App\Models\Color;
use App\Models\Product;
use App\Models\User;
use App\Models\Wishlist;
use App\Utility\ProductUtility;
use Combinations;
use Illuminate\Support\Str;

class ProductService
{
    public function store(array $data)
    {
        $collection = collect($data);

        $user_id = auth()->user()->user_type === 'seller'
            ? auth()->user()->id
            : User::where('user_type', 'admin')->first()->id;

        $approved = (auth()->user()->user_type === 'seller' && get_setting('product_approve_by_admin') == 1) ? 0 : 1;

        // Process tags
        $tags = !empty($collection['tags'][0])
            ? implode(',', array_map(fn($tag) => $tag->value, json_decode($collection['tags'][0])))
            : '';
        $collection['tags'] = $tags;

        // Handle discount dates
        [$discount_start_date, $discount_end_date] = !empty($collection['date_range'])
            ? array_map('strtotime', explode(' to ', $collection['date_range']))
            : [null, null];
        unset($collection['date_range']);

        // Set default meta data
        $collection['meta_title'] = $collection['meta_title'] ?? $collection['name'];
        $collection['meta_description'] = $collection['meta_description'] ?? strip_tags($collection['description']);
        $collection['meta_img'] = $collection['meta_img'] ?? $collection['thumbnail_img'];

        // Handle shipping cost
        $shipping_cost = isset($collection['shipping_type']) && $collection['shipping_type'] === 'flat_rate'
            ? $collection['flat_shipping_cost']
            : 0;
        unset($collection['flat_shipping_cost']);

        // Create slug
        $slug = Str::slug($collection['name']);
        $same_slug_count = Product::where('slug', 'LIKE', $slug . '%')->count();
        $slug .= $same_slug_count ? '-' . ($same_slug_count + 1) : '';

        // Handle colors
        $colors = !empty($collection['colors_active']) && !empty($collection['colors'])
            ? json_encode($collection['colors'])
            : json_encode([]);

        // Generate combinations
        $options = ProductUtility::get_attribute_options($collection);
        $combinations = (new CombinationService())->generate_combination($options);

        if (!empty($combinations)) {
            foreach ($combinations as $combination) {
                $str = ProductUtility::get_combination_string($combination, $collection);
                unset(
                    $collection['price_' . str_replace('.', '_', $str)],
                    $collection['sku_' . str_replace('.', '_', $str)],
                    $collection['qty_' . str_replace('.', '_', $str)],
                    $collection['img_' . str_replace('.', '_', $str)]
                );
            }
        }
        unset($collection['colors_active']);

        // Handle choice options
        $choice_options = array();
        if (isset($collection['choice_no']) && $collection['choice_no']) {
            $str = '';
            $item = array();
            foreach ($collection['choice_no'] as $key => $no) {
                $str = 'choice_options_' . $no;
                $item['attribute_id'] = $no;
                $attribute_data = array();
                // foreach (json_decode($request[$str][0]) as $key => $eachValue) {
                foreach ($collection[$str] as $key => $eachValue) {
                    // array_push($data, $eachValue->value);
                    array_push($attribute_data, $eachValue);
                }
                unset($collection[$str]);

                $item['values'] = $attribute_data;
                array_push($choice_options, $item);
            }
        }

        $choice_options = json_encode($choice_options, JSON_UNESCAPED_UNICODE);

        if (isset($collection['choice_no']) && $collection['choice_no']) {
            $attributes = json_encode($collection['choice_no']);
            unset($collection['choice_no']);
        } else {
            $attributes = json_encode(array());
        }

        // Check publication status
        $published = !in_array($collection['button'], ['unpublish', 'draft']);
        unset($collection['button']);

        // Handle custom attributes
        $customAttributes = [];
        if (isset($collection['product_service_custom_data']) && is_array($collection['product_service_custom_data'])) {
            foreach ($collection['product_service_custom_data'] as $attribute) {
                $customAttributes[] = [
                    'key' => $attribute['key'],
                    'value' => $attribute['value']
                ];
            }
        }
        unset($collection['product_service_custom_data']);

        $data = $collection->merge(compact(
            'user_id',
            'approved',
            'discount_start_date',
            'discount_end_date',
            'shipping_cost',
            'slug',
            'colors',
            'choice_options',
            'attributes',
            'published'
        ))->toArray();
        $data['product_service_custom_data'] = ($customAttributes);

        return Product::create($data);
    }

    public function update(array $data, Product $product)
    {
        $collection = collect($data);

        // Create or update slug
        $slug = $collection['slug'] ? Str::slug($collection['slug']) : Str::slug($collection['name']);
        $same_slug_count = Product::where('slug', 'LIKE', $slug . '%')->count();
        $slug .= $same_slug_count > 1 ? '-' . $same_slug_count + 1 : '';

        // Default settings for flags
        $flags = ['refundable', 'is_quantity_multiplied', 'cash_on_delivery', 'featured', 'todays_deal'];
        foreach ($flags as $flag) {
            $collection[$flag] = $collection[$flag] ?? 0;
        }

        // Process tags
        $tags = !empty($collection['tags'][0])
            ? implode(',', array_map(fn($tag) => $tag->value, json_decode($collection['tags'][0])))
            : '';
        $collection['tags'] = $tags;

        // Handle discount dates
        [$discount_start_date, $discount_end_date] = $collection['date_range']
            ? array_map('strtotime', explode(' to ', $collection['date_range']))
            : [null, null];
        unset($collection['date_range']);

        // Set meta data defaults
        $collection['meta_title'] = $collection['meta_title'] ?? $collection['name'];
        $collection['meta_description'] = $collection['meta_description'] ?? strip_tags($collection['description']);
        $collection['meta_img'] = $collection['meta_img'] ?? $collection['thumbnail_img'];

        if ($collection['lang'] != env("DEFAULT_LANGUAGE")) {
            $collection = $collection->except(['name', 'unit', 'description']);
        }
        unset($collection['lang']);

        // Handle shipping cost
        if (isset($collection['shipping_type'])) {
            $shipping_cost = $collection['shipping_type'] === 'flat_rate' ? $collection['flat_shipping_cost'] : 0;
            unset($collection['flat_shipping_cost']);
        } else {
            $shipping_cost = 0;
        }

        // Handle colors
        $colors = !empty($collection['colors_active']) && !empty($collection['colors'])
            ? json_encode($collection['colors'])
            : json_encode([]);

        // Generate combinations
        $options = ProductUtility::get_attribute_options($collection);
        $combinations = (new CombinationService())->generate_combination($options);

        foreach ($combinations as $combination) {
            $str = ProductUtility::get_combination_string($combination, $collection);
            $combination_key = str_replace('.', '_', $str);
            unset(
                $collection['price_' . $combination_key],
                $collection['sku_' . $combination_key],
                $collection['qty_' . $combination_key],
                $collection['img_' . $combination_key]
            );
        }
        unset($collection['colors_active']);

        // Handle choice options
        $choice_options = array();
        if (isset($collection['choice_no']) && $collection['choice_no']) {
            $str = '';
            $item = array();
            foreach ($collection['choice_no'] as $key => $no) {
                $str = 'choice_options_' . $no;
                $item['attribute_id'] = $no;
                $attribute_data = array();
                // foreach (json_decode($request[$str][0]) as $key => $eachValue) {
                foreach ($collection[$str] as $key => $eachValue) {
                    // array_push($data, $eachValue->value);
                    array_push($attribute_data, $eachValue);
                }
                unset($collection[$str]);

                $item['values'] = $attribute_data;
                array_push($choice_options, $item);
            }
        }

        $choice_options = json_encode($choice_options, JSON_UNESCAPED_UNICODE);

        if (isset($collection['choice_no']) && $collection['choice_no']) {
            $attributes = json_encode($collection['choice_no']);
            unset($collection['choice_no']);
        } else {
            $attributes = json_encode(array());
        }

        // Handle custom attributes
        $customAttributes = [];
        if (isset($collection['product_service_custom_data']) && is_array($collection['product_service_custom_data'])) {
            foreach ($collection['product_service_custom_data'] as $attribute) {
                $customAttributes[] = [
                    'key' => $attribute['key'],
                    'value' => $attribute['value']
                ];
            }
        }
        unset($collection['product_service_custom_data']);

        $data = $collection->merge(compact(
            'discount_start_date',
            'discount_end_date',
            'shipping_cost',
            'slug',
            'colors',
            'choice_options',
            'attributes'
        ))->toArray();

        $data['product_service_custom_data'] = ($customAttributes);


        $product->update($data);

        return $product;
    }


    public function product_duplicate_store($product)
    {
        $product_new = $product->replicate();
        $product_new->slug = $product_new->slug . '-' . Str::random(5);
        $product_new->approved = (get_setting('product_approve_by_admin') == 1 && $product->added_by != 'admin') ? 0 : 1;
        $product_new->save();

        return $product_new;
    }

    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $product->product_translations()->delete();
        $product->categories()->detach();
        $product->stocks()->delete();
        $product->taxes()->delete();
        $product->wishlists()->delete();
        $product->carts()->delete();
        $product->frequently_bought_products()->delete();
        $product->last_viewed_products()->delete();
        Product::destroy($id);
    }

    public function product_search(array $data)
    {
        $collection     = collect($data);
        $auth_user      = auth()->user();
        $productType    = $collection['product_type'];
        $products       = Product::query();

        if ($collection['category'] != null) {
            $category = Category::with('childrenCategories')->find($collection['category']);
            $products = $category->products();
        }

        $products = in_array($auth_user->user_type, ['admin', 'staff']) ? $products->where('products.added_by', 'admin') : $products->where('products.user_id', $auth_user->id);
        $products->where('published', '1')->where('auction_product', 0)->where('approved', '1');

        if ($productType == 'physical') {
            $products->where('digital', 0)->where('wholesale_product', 0);
        } elseif ($productType == 'digital') {
            $products->where('digital', 1);
        } elseif ($productType == 'service') {
            $products->where('digital', 2);
        } elseif ($productType == 'wholesale') {
            $products->where('wholesale_product', 1);
        }

        if ($collection['product_id'] != null) {
            $products->where('id', '!=', $collection['product_id']);
        }

        if ($collection['search_key'] != null) {
            $products->where('name', 'like', '%' . $collection['search_key'] . '%');
        }

        return $products->limit(20)->get();
    }

    public function setCategoryWiseDiscount(array $data)
    {
        $auth_user      = auth()->user();
        $discount_start_date = null;
        $discount_end_date   = null;
        if ($data['date_range'] != null) {
            $date_var               = explode(" to ", $data['date_range']);
            $discount_start_date = strtotime($date_var[0]);
            $discount_end_date   = strtotime($date_var[1]);
        }
        $seller_product_discount =  isset($data['seller_product_discount']) ? $data['seller_product_discount'] : null;
        $admin_id = User::where('user_type', 'admin')->first()->id;

        $products = Product::where('category_id', $data['category_id'])->where('auction_product', 0);
        if (in_array($auth_user->user_type, ['admin', 'staff']) && $seller_product_discount == 0) {
            $products = $products->where('user_id', $admin_id);
        } elseif ($auth_user->user_type == 'seller') {
            $products = $products->where('user_id', $auth_user->id);
        }

        $products->update([
            'discount' => $data['discount'],
            'discount_type' => 'percent',
            'discount_start_date' => $discount_start_date,
            'discount_end_date' => $discount_end_date,
        ]);
        return 1;
    }
}
