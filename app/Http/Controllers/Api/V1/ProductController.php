<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\BrandCollection;
use Cache;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Utility\SearchUtility;
use App\Utility\CategoryUtility;
use App\Http\Resources\V1\ProductCollection;
use App\Http\Resources\V1\ProductMiniCollection;
use App\Http\Resources\V1\ProductDetailCollection;
use App\Http\Resources\V1\DigitalProductDetailCollection;
use App\Models\Brand;
use App\Models\Category;
use App\Models\CustomerProduct;
use App\Models\Color;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        // 1. Determine how many items per page (default to 10)
        $perPage = $request->query('count_per_page', 10);
        $page = $request->query('page', 1);
        
        // 2. Handle type-specific conditions
        $isPackage = $request->query('type') === 'package';
        $isSession = $request->query('type') === 'session';

        // 3. Build base query with published products only
        $query = Product::query()
            ->where('published', 1)
            ->when($isPackage, fn ($q) => $q->with('packageDetails'))
            ->when($isSession, fn ($q) => $q->with('packageDetails'))
            ->withAvg('reviews as avg_rating', 'rating')
            ->with(['brand', 'category']);

        // 4. Search functionality
        if ($request->filled('search')) {
            $searchTerm = $request->query('search');
            $query->where(function ($q) use ($searchTerm) {
                foreach (explode(' ', trim($searchTerm)) as $word) {
                    $q->where('name', 'like', '%' . $word . '%')
                      ->orWhere('tags', 'like', '%' . $word . '%')
                      ->orWhereHas('product_translations', function ($subQuery) use ($word) {
                          $subQuery->where('name', 'like', '%' . $word . '%');
                      });
                }
            });
        }

        // 5. Filter by type (simple, physical, digital, bundle, package, session)
        if ($request->filled('type')) {
            $type = $request->query('type');
            // Handle 'simple' type mapping to 'physical'
            if ($type === 'simple') {
                $type = 'physical';
            }
            $query->where('type', $type);
        }

        // 6. Filter by categories (comma-separated category slugs)
        if ($request->filled('categories')) {
            $categoryIdentifiers = explode(',', $request->query('categories'));
            $categoryIds = [];
            
            foreach ($categoryIdentifiers as $identifier) {
                // Try to find category by slug first, then by ID
                $category = Category::where('slug', $identifier)
                                  ->orWhere('id', $identifier)
                                  ->first();
                if ($category) {
                    $categoryIds[] = $category->id;
                    // Include child category IDs
                    $categoryIds = array_merge($categoryIds, CategoryUtility::children_ids($category->id));
                }
            }
            
            if (!empty($categoryIds)) {
                $query->whereIn('category_id', array_unique($categoryIds));
            }
        }

        // 7. Filter by price range
        if ($request->filled('price_min')) {
            $priceMin = floatval($request->query('price_min'));
            $query->where(function ($q) use ($priceMin) {
                $q->where('unit_price', '>=', $priceMin)
                  ->orWhere('sale_price', '>=', $priceMin);
            });
        }
        
        if ($request->filled('price_max')) {
            $priceMax = floatval($request->query('price_max'));
            $query->where(function ($q) use ($priceMax) {
                $q->where('unit_price', '<=', $priceMax)
                  ->orWhere('sale_price', '<=', $priceMax);
            });
        }

        // 8. Filter by sale status (on_sale=true)
        if ($request->boolean('on_sale')) {
            $query->where(function ($q) {
                $q->where('sale_price', '>', 0)
                  ->whereColumn('sale_price', '<', 'unit_price');
            });
        }

        // 9. Filter by top-selling flag
        if ($request->boolean('is_top_selling')) {
            $query->where('is_top_selling', true);
        }

        // 10. Handle sorting
        $sortBy = $request->query('sort_by', 'created_at');
        $sortOrder = $request->query('sort_order', 'desc');

        switch ($sortBy) {
            case 'price':
                $query->orderBy('unit_price', $sortOrder);
                break;
            case 'name':
                $query->orderBy('name', $sortOrder);
                break;
            case 'rating':
                $query->orderBy('rating', $sortOrder);
                break;
            case 'popularity':
            case 'num_of_sale':
                $query->orderBy('num_of_sale', $sortOrder);
                break;
            case 'created_at':
            default:
                $query->orderBy('created_at', $sortOrder);
                break;
        }

        // 11. Paginate results
        $products = $query->paginate($perPage, ['*'], 'page', $page);

        // 12. Return wrapped collection
        return new ProductMiniCollection($products);
    }


    public function show($slug)
    {
        $product = Product::where('slug', $slug)
            ->withCount([
                'reviews as one_star_count'   => fn ($q) => $q->where('rating', 1),
                'reviews as two_star_count'   => fn ($q) => $q->where('rating', 2),
                'reviews as three_star_count' => fn ($q) => $q->where('rating', 3),
                'reviews as four_star_count'  => fn ($q) => $q->where('rating', 4),
                'reviews as five_star_count'  => fn ($q) => $q->where('rating', 5),
            ])
            ->withAvg('reviews as avg_rating', 'rating')
            ->with('reviews.user')
            ->firstOrFail();
        // round the avg_rating to one decimal place (or leave as-is)
        $avg = $product->avg_rating !== null
            ? round($product->avg_rating, 1)
            : 0;

        $counts = [
            1 => $product->one_star_count,
            2 => $product->two_star_count,
            3 => $product->three_star_count,
            4 => $product->four_star_count,
            5 => $product->five_star_count,
        ];

        $total = array_sum($counts);
        $pct   = array_map(fn ($c) => $total ? round($c / $total * 100, 1) : 0, $counts);
        $reviews_info = [
            'total_reviews' => $total,
            'percentage' => $pct,
            'stars_counts' => $counts,
            'avg_rating'    => $avg,
        ];
        $bundleItems = $product->bundleItems;

        return response()->json(compact('product', 'reviews_info', 'bundleItems'));
    }

    public function getPrice(Request $request)
    {
        $product = Product::where("slug", $request->slug)->first();
        $str = '';
        $tax = 0;
        $quantity = 1;

        if ($request->has('quantity') && $request->quantity != null) {
            $quantity = $request->quantity;
        }

        if ($request->has('color') && $request->color != null) {
            $str = Color::where('code', '#' . $request->color)->first()->name;
        }

        $var_str = str_replace(',', '-', $request->variants);
        $var_str = str_replace(' ', '', $var_str);

        if ($var_str != "") {
            $temp_str = $str == "" ? $var_str : '-' . $var_str;
            $str .= $temp_str;
        }

        $product_stock = $product->stocks->where('variant', $str)->first();
        $price = $product_stock->price;


        if ($product->wholesale_product) {
            $wholesalePrice = $product_stock->wholesalePrices->where('min_qty', '<=', $quantity)->where('max_qty', '>=', $quantity)->first();
            if ($wholesalePrice) {
                $price = $wholesalePrice->price;
            }
        }

        $stock_qty = $product_stock->qty;
        $stock_txt = $product_stock->qty;
        $max_limit = $product_stock->qty;

        if ($stock_qty >= 1 && $product->min_qty <= $stock_qty) {
            $in_stock = 1;
        } else {
            $in_stock = 0;
        }

        //Product Stock Visibility
        if ($product->stock_visibility_state == 'text') {
            if ($stock_qty >= 1 && $product->min_qty < $stock_qty) {
                $stock_txt = translate('In Stock');
            } else {
                $stock_txt = translate('Out Of Stock');
            }
        }

        //discount calculation
        $discount_applicable = false;

        if ($product->discount_start_date == null) {
            $discount_applicable = true;
        } elseif (
            strtotime(date('d-m-Y H:i:s')) >= $product->discount_start_date &&
            strtotime(date('d-m-Y H:i:s')) <= $product->discount_end_date
        ) {
            $discount_applicable = true;
        }

        if ($discount_applicable) {
            if ($product->discount_type == 'percent') {
                $price -= ($price * $product->discount) / 100;
            } elseif ($product->discount_type == 'amount') {
                $price -= $product->discount;
            }
        }

        // taxes
        foreach ($product->taxes as $product_tax) {
            if ($product_tax->tax_type == 'percent') {
                $tax += ($price * $product_tax->tax) / 100;
            } elseif ($product_tax->tax_type == 'amount') {
                $tax += $product_tax->tax;
            }
        }

        $price += $tax;

        return response()->json(

            [
                'result' => true,
                'data' => [
                    'price' => single_price($price * $quantity),
                    'stock' => $stock_qty,
                    'stock_txt' => $stock_txt,
                    'digital' => $product->digital,
                    'variant' => $str,
                    'variation' => $str,
                    'max_limit' => $max_limit,
                    'in_stock' => $in_stock,
                    'image' => $product_stock->image == null ? "" : uploaded_asset($product_stock->image),

                ]

            ]
        );
    }


    public function category($id, Request $request)
    {
        $category = Category::where('slug', $id)->first();

        $category_ids = CategoryUtility::children_ids($category->id);
        $category_ids[] = $category->id;

        $products = Product::whereIn('category_id', $category_ids)->physical();

        if ($request->name != "" || $request->name != null) {
            $products = $products->where('name', 'like', '%' . $request->name . '%');
        }

        return new ProductMiniCollection(filter_products($products)->latest()->paginate(10));
    }


    public function brand($slug, Request $request)
    {
        $brand = Brand::where('slug', $slug)->first();
        $products = Product::where('brand_id', $brand->id)->physical();
        if ($request->name != "" || $request->name != null) {
            $products = $products->where('name', 'like', '%' . $request->name . '%');
        }
        return new ProductMiniCollection(filter_products($products)->latest()->paginate(10));
    }

    public function getBrands()
    {
        $brands = Brand::all();

        return BrandCollection::collection($brands);
    }

    public function todaysDeal()
    {
        $products = Product::where('todays_deal', 1)->physical();
        return new ProductMiniCollection(filter_products($products)->limit(20)->latest()->get());
    }

    public function featured()
    {
        $products = Product::where('featured', 1)->physical();
        return new ProductMiniCollection(filter_products($products)->latest()->paginate(10));
    }

    public function inhouse()
    {
        $products = Product::where('added_by', 'admin');
        return new ProductMiniCollection(filter_products($products)->latest()->paginate(12));
    }

    public function digital()
    {
        $products = Product::digital();
        return new ProductMiniCollection(filter_products($products)->latest()->paginate(10));
    }

    public function bestSeller()
    {
        $products = Product::orderBy('num_of_sale', 'desc')->physical();
        return new ProductMiniCollection(filter_products($products)->limit(20)->get());
    }

    public function related($slug)
    {
        $product = Product::where("slug", $slug)->first();
        $products = Product::where('category_id', $product->category_id)->where('id', '!=', $slug)->physical();
        return new ProductMiniCollection(filter_products($products)->limit(10)->get());
    }


    public function search(Request $request)
    {
        $category_ids = [];
        $brand_ids = [];

        if ($request->categories != null && $request->categories != "") {
            $category_ids = explode(',', $request->categories);
        }

        if ($request->brands != null && $request->brands != "") {
            $brand_ids = explode(',', $request->brands);
        }

        $sort_by = $request->sort_key;
        $name = $request->name;
        $min = $request->min;
        $max = $request->max;


        $products = Product::query();

        $products->where('published', 1)->physical();

        if (!empty($brand_ids)) {
            $products->whereIn('brand_id', $brand_ids);
        }

        if (!empty($category_ids)) {
            $n_cid = [];
            foreach ($category_ids as $cid) {
                $n_cid = array_merge($n_cid, CategoryUtility::children_ids($cid));
            }

            if (!empty($n_cid)) {
                $category_ids = array_merge($category_ids, $n_cid);
            }

            $products->whereIn('category_id', $category_ids);
        }

        if ($name != null && $name != "") {
            $products->where(function ($query) use ($name) {
                foreach (explode(' ', trim($name)) as $word) {
                    $query->where('name', 'like', '%' . $word . '%')->orWhere('tags', 'like', '%' . $word . '%')->orWhereHas('product_translations', function ($query) use ($word) {
                        $query->where('name', 'like', '%' . $word . '%');
                    });
                }
            });
            SearchUtility::store($name);
            $case1 = $name . '%';
            $case2 = '%' . $name . '%';

            $products->orderByRaw("CASE 
                WHEN name LIKE '$case1' THEN 1 
                WHEN name LIKE '$case2' THEN 2 
                ELSE 3 
                END");
        }

        if ($min != null && $min != "" && is_numeric($min)) {
            $products->where('unit_price', '>=', $min);
        }

        if ($max != null && $max != "" && is_numeric($max)) {
            $products->where('unit_price', '<=', $max);
        }



        switch ($sort_by) {
            case 'price_low_to_high':
                $products->orderBy('unit_price', 'asc');
                break;

            case 'price_high_to_low':
                $products->orderBy('unit_price', 'desc');
                break;

            case 'new_arrival':
                $products->orderBy('created_at', 'desc');
                break;

            case 'popularity':
                $products->orderBy('num_of_sale', 'desc');
                break;

            case 'top_rated':
                $products->orderBy('rating', 'desc');
                break;

            default:
                $products->orderBy('created_at', 'desc');
                break;
        }

        return new ProductMiniCollection(filter_products($products)->paginate(10));
    }

    public function variantPrice(Request $request)
    {
        $product = Product::findOrFail($request->id);
        $str = '';
        $tax = 0;

        if ($request->has('color') && $request->color != "") {
            $str = Color::where('code', '#' . $request->color)->first()->name;
        }

        $var_str = str_replace(',', '-', $request->variants);
        $var_str = str_replace(' ', '', $var_str);

        if ($var_str != "") {
            $temp_str = $str == "" ? $var_str : '-' . $var_str;
            $str .= $temp_str;
        }
        return   $this->calc($product, $str, $request, $tax);
    }
}
