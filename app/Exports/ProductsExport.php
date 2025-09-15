<?php

namespace App\Exports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ProductsExport implements FromCollection, WithHeadings, WithMapping
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return Product::with(['categories', 'brand', 'main_category'])
            ->where('auction_product', 0)
            ->where('wholesale_product', 0)
            ->get();
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'ID',
            'Name',
            'SKU',
            'Type',
            'Unit Price',
            'Current Stock',
            'Is Top Selling',
            'Published',
            'Brand',
            'Main Category',
            'Categories',
            'Created At',
        ];
    }

    /**
     * @param mixed $product
     * @return array
     */
    public function map($product): array
    {
        return [
            $product->id,
            $product->name,
            $product->sku,
            $product->type,
            $product->unit_price,
            $product->current_stock,
            $product->is_top_selling ? 'Yes' : 'No',
            $product->published ? 'Yes' : 'No',
            $product->brand->name ?? '',
            $product->main_category->name ?? '',
            $product->categories->pluck('name')->implode(', '),
            $product->created_at->format('Y-m-d H:i:s'),
        ];
    }
}