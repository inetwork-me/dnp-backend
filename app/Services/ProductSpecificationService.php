<?php

namespace App\Services;

use App\Models\ProductSpecification;
use App\Models\ProductSpecificationTranslation;
use App\Models\ProductTax;

class ProductSpecificationService
{
    public function store(array $data)
    {
        $collection = collect($data);
        // dd($collection);
        if ($collection['spec_key']) {
            for ($i = 0; $i < count($collection['spec_key']); $i++) {
                $product_specification = new ProductSpecification();
                $product_specification->spec_key = $collection['spec_key'][$i];
                $product_specification->spec_value = $collection['spec_value'][$i];
                $product_specification->product_id = $collection['product_id'];
                $product_specification->save();
            }


            ProductSpecificationTranslation::updateOrCreate(
                [
                    'product_specification_id' => $product_specification->id,
                    'spec_key' => $product_specification->spec_key,
                    'spec_value' => $product_specification->spec_value,
                    'lang' => $collection['lang'],
                ]
            );
        }
    }

    public function product_duplicate_store($product_taxes, $product_new)
    {
        foreach ($product_taxes as $key => $tax) {
            $product_tax = new ProductTax;
            $product_tax->product_id = $product_new->id;
            $product_tax->tax_id = $tax->tax_id;
            $product_tax->tax = $tax->tax;
            $product_tax->tax_type = $tax->tax_type;
            $product_tax->save();
        }
    }
}
