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
        return Product::with(['categories', 'brand', 'main_category', 'bundleItems', 'product_translations'])
            ->whereIn('type', ['simple', 'bundle'])
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
            'Type',
            'Slug',
            'Name (EN)',
            'Name (AR)',
            'Description (EN)',
            'Description (AR)',
            'SKU',
            'Brand',
            'Main Category',
            'Categories',
            'Unit Price',
            'Discount',
            'Current Stock',
            'Unit',
            'Is Top Selling',
            'Published',
            'Thumbnail URL',
            'Frontend URL',
            'Bundle Items (if bundle)',
            'Created At',
        ];
    }

    /**
     * @param mixed $product
     * @return array
     */
    public function map($product): array
    {
        // Get product name in English and Arabic
        $nameEn = $product->product_translations->where('lang', 'en')->first()->name ?? $product->name ?? '';
        $nameAr = $product->product_translations->where('lang', 'ar')->first()->name ?? '';

        // Get descriptions from desc JSON field (supports multiple languages)
        $descEn = '';
        $descAr = '';
        if (is_array($product->desc)) {
            $descEn = $product->desc['en'] ?? '';
            $descAr = $product->desc['ar'] ?? '';
        }

        // Build bundle items string
        $bundleItems = '';
        if ($product->type === 'bundle' && $product->bundleItems->count() > 0) {
            $items = [];
            foreach ($product->bundleItems as $item) {
                $itemNameEn = $item->product_translations->where('lang', 'en')->first()->name ?? $item->name ?? 'Unknown';
                $items[] = $itemNameEn . ' (x' . $item->pivot->quantity . ')';
            }
            $bundleItems = implode(', ', $items);
        }

        // Get thumbnail URL
        $thumbnailUrl = '';
        if (isset($product->thumbnail['path'])) {
            $thumbnailUrl = env('APP_URL') . '/storage/' . $product->thumbnail['path'];
        }

        // Get frontend product URL
        $frontendUrl = '';
        if ($product->slug) {
            $frontendUrl = env('FRONTEND_URL') . '/products/' . $product->slug;
        }

        return [
            $product->id,
            $product->type,
            $product->slug ?? '',
            $nameEn,
            $nameAr,
            $descEn,
            $descAr,
            $product->sku ?? '',
            $product->brand->name ?? '',
            $product->main_category->name ?? '',
            $product->categories->pluck('name')->implode(', '),
            $product->unit_price,
            $product->discount ?? 0,
            $product->current_stock ?? 0,
            $product->unit ?? '',
            $product->is_top_selling ? 'Yes' : 'No',
            $product->published ? 'Yes' : 'No',
            $thumbnailUrl,
            $frontendUrl,
            $bundleItems,
            $product->created_at->format('Y-m-d H:i:s'),
        ];
    }
}