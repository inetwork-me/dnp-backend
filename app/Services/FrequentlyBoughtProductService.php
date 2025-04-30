<?php

namespace App\Services;

use App\Models\FrequentlyBoughtProduct;
use DB;

class FrequentlyBoughtProductService
{
    public function store(array $data)
    {
        $collection = collect($data);
        
        if(isset($collection['fq_bought_product_ids']) && 
            $collection['fq_bought_product_ids'] != null && 
                $collection['frequently_bought_selection_type'] == 'product' ){
            foreach($collection['fq_bought_product_ids'] as $fq_product){

                FrequentlyBoughtProduct::insert([
                    'product_id' => $collection['product_id'],
                    'frequently_bought_product_id' => $fq_product,
                ]);
            }
        }
        elseif(isset($collection['fq_bought_product_category_id']) && 
                $collection['fq_bought_product_category_id'] != null && 
                    $collection['frequently_bought_selection_type'] == 'category') {
            FrequentlyBoughtProduct::insert([
                'product_id' => $collection['product_id'],
                'category_id' => $collection['fq_bought_product_category_id'],
            ]);
        }
        
    }

    public function product_duplicate_store($frequently_bought_products, $product_new)
    {
        foreach ($frequently_bought_products as $fqb_product) {
            FrequentlyBoughtProduct::insert([
                'product_id' => $product_new->id,
                'frequently_bought_product_id' => $fqb_product->frequently_bought_product_id,
                'category_id' => $fqb_product->category_id,
            ]);
        }
    }
}