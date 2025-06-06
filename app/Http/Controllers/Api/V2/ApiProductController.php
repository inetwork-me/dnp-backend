<?php
// app/Http/Controllers/Api/V2/ApiProductController.php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Services\ProductService;
use App\Services\ProductStockService;
use App\Services\ProductTaxService;
use App\Services\ProductFlashDealService;
use App\Services\FrequentlyBoughtProductService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ApiProductController extends Controller
{
    protected $productService;
    protected $productTaxService;
    protected $productFlashDealService;
    protected $productStockService;
    protected $frequentlyBoughtProductService;

    public function __construct(
        ProductService $productService,
        ProductTaxService $productTaxService,
        ProductFlashDealService $productFlashDealService,
        ProductStockService $productStockService,
        FrequentlyBoughtProductService $frequentlyBoughtProductService
    ) {
        $this->productService                 = $productService;
        $this->productTaxService              = $productTaxService;
        $this->productFlashDealService        = $productFlashDealService;
        $this->productStockService            = $productStockService;
        $this->frequentlyBoughtProductService = $frequentlyBoughtProductService;
    }

    /**
     * GET /api/products
     * List all products (paginated). Optional query params:
     *  - page (int), per_page (int),
     *  - search (string), sort_by (string like "name"), sort_order ("asc"/"desc")
     */
    public function index(Request $request): JsonResponse
    {
        $perPage   = $request->query('per_page', 15);
        $search    = $request->query('search', null);
        $sortBy    = $request->query('sort_by', 'created_at');
        $sortOrder = $request->query('sort_order', 'desc');

        $query = Product::query()
            ->with(['categories', 'stocks', 'taxes']) // eager-load relationships as needed
            ->where('auction_product', 0)
            ->where('wholesale_product', 0);

        if (!empty($search)) {
            $query->where('name', 'like', "%{$search}%")
                ->orWhereHas('stocks', function ($q) use ($search) {
                    $q->where('sku', 'like', "%{$search}%");
                });
        }

        $query->orderBy($sortBy, $sortOrder);

        $products = $query->paginate($perPage);

        // Wrap in a resource collection if you want consistent JSON format
        return response()->json([
            'data' => $products->items(),
            'meta' => [
                'current_page' => $products->currentPage(),
                'last_page'    => $products->lastPage(),
                'per_page'     => $products->perPage(),
                'total'        => $products->total(),
            ],
        ]);
    }

    /**
     * GET /api/products/{product}
     * Return a single product (with relationships).
     */
    public function show(Product $product): JsonResponse
    {
        $product->load(['categories', 'stocks', 'taxes', 'frequently_bought_products']);
        return response()->json([
            'data' => $product,
        ]);
    }

    /**
     * POST /api/products
     * Create a new product. The request is validated by ProductRequest.
     */
    public function store(ProductRequest $request): JsonResponse
    {
        $payload = $request->except([
            '_token', 'sku', 'choice', 'tax_id', 'tax', 'tax_type',
            'flash_deal_id', 'flash_discount', 'flash_discount_type'
        ]);

        // 4) Decode the Base64‐encoded thumbnail_img
        $base64String = $payload['thumbnail_img'];
        if (!preg_match('/^data:image\/(\w+);base64,/', $base64String, $typeMatch)) {
            return response()->json([
                'success' => false,
                'errors'  => ['Logo must be a valid Base64‐encoded image string.'],
            ], 422);
        }
        $imageType = strtolower($typeMatch[1]); // e.g. png, jpeg, jpg, gif

        $base64Data = substr($base64String, strpos($base64String, ',') + 1);
        $decodedImage = base64_decode($base64Data);
        if ($decodedImage === false) {
            return response()->json([
                'success' => false,
                'errors'  => ['Failed to decode Base64 image data.'],
            ], 422);
        }

        // 5) Generate a unique filename and store under public/uploads/brands/
        $productname = $payload['name'];
        $filename = $productname . Str::random(10) . '.' . $imageType;
        $path = 'uploads/products/' . $productname . '/' . $filename;
        Storage::disk('public')->put($path, $decodedImage);
        $thumbnail_img = Storage::url($path);
        $payload['thumbnail_img'] = $thumbnail_img;
        $product = $this->productService->store($payload);

        // Attach categories
        $product->categories()->attach($request->category_ids);

        // VAT & Tax
        if ($request->tax_id) {
            $this->productTaxService->store($request->only([
                'tax_id', 'tax', 'tax_type', 'product_id'
            ]) + ['product_id' => $product->id]);
        }

        // Flash Deal
        $this->productFlashDealService->store(
            $request->only([
                'flash_deal_id', 'flash_discount', 'flash_discount_type'
            ]),
            $product
        );

        // Product Stock
        $this->productStockService->store(
            $request->only([
                'colors_active', 'colors', 'choice_no', 'unit_price',
                'sku', 'current_stock', 'product_id'
            ]) + ['product_id' => $product->id],
            $product
        );

        // Frequently Bought Products
        $this->frequentlyBoughtProductService->store(
            $request->only([
                'product_id', 'frequently_bought_selection_type',
                'fq_bought_product_ids', 'fq_bought_product_category_id'
            ]) + ['product_id' => $product->id]
        );

        // Product Translations
        $request->merge(['lang' => env('DEFAULT_LANGUAGE')]);
        \App\Models\ProductTranslation::create($request->only([
            'lang', 'name', 'unit', 'description', 'product_id'
        ]) + ['product_id' => $product->id]);

        // Product Specifications (loop through key_* fields)
        foreach ($request->all() as $key => $value) {
            if (str_starts_with($key, 'key_')) {
                $idx       = substr($key, 4);
                $valueKey  = 'value_' . $idx;
                $valueVal  = $request->$valueKey;
                DB::table('product_specifications')->insert([
                    'product_id'  => $product->id,
                    'key'         => $value,
                    'value'       => $valueVal,
                    'language_id' => $request->lang,
                ]);
            }
        }

        // Artisan::call('view:clear');
        // Artisan::call('cache:clear');

        return response()->json([
            'data' => $product,
            'message' => 'Product created successfully',
        ], 201);
    }

    /**
     * PUT /api/products/{product}
     * Update an existing product.
     */
    public function update(ProductRequest $request, Product $product): JsonResponse
    {
        $payload = $request->except([
            '_token', 'sku', 'choice', 'tax_id', 'tax', 'tax_type',
            'flash_deal_id', 'flash_discount', 'flash_discount_type'
        ]);

        $this->productService->update($payload, $product);

        // Sync categories
        $product->categories()->sync($request->category_ids);

        // Rebuild stock
        $product->stocks()->delete();
        $this->productStockService->store(
            $request->only([
                'colors_active', 'colors', 'choice_no', 'unit_price',
                'sku', 'current_stock', 'product_id'
            ]) + ['product_id' => $product->id],
            $product
        );

        // Flash Deal
        $product->taxes()->delete();
        $this->productFlashDealService->store(
            $request->only([
                'flash_deal_id', 'flash_discount', 'flash_discount_type'
            ]),
            $product
        );

        // VAT & Tax
        if ($request->tax_id) {
            $product->taxes()->delete();
            $this->productTaxService->store(
                $request->only([
                    'tax_id', 'tax', 'tax_type', 'product_id'
                ]) + ['product_id' => $product->id]
            );
        }

        // Frequently Bought Products
        $product->frequently_bought_products()->delete();
        $this->frequentlyBoughtProductService->store(
            $request->only([
                'product_id', 'frequently_bought_selection_type',
                'fq_bought_product_ids', 'fq_bought_product_category_id'
            ]) + ['product_id' => $product->id]
        );

        // Translations (update or create)
        \App\Models\ProductTranslation::updateOrCreate(
            $request->only(['lang', 'product_id']),
            $request->only(['name', 'unit', 'description'])
        );

        // Rebuild specifications for this language
        DB::table('product_specifications')
            ->where('product_id', $product->id)
            ->where('language_id', $request->lang)
            ->delete();

        foreach ($request->all() as $key => $value) {
            if (str_starts_with($key, 'key_')) {
                $idx       = substr($key, 4);
                $valueKey  = 'value_' . $idx;
                $valueVal  = $request->$valueKey;
                DB::table('product_specifications')->insert([
                    'product_id'  => $product->id,
                    'key'         => $value,
                    'value'       => $valueVal,
                    'language_id' => $request->lang,
                ]);
            }
        }

        // Artisan::call('view:clear');
        // Artisan::call('cache:clear');

        return response()->json([
            'data'    => $product->fresh(['categories', 'stocks', 'taxes']),
            'message' => 'Product updated successfully',
        ]);
    }

    /**
     * DELETE /api/products/{product}
     * Delete a product (and cascade related).
     */
    public function destroy(Product $product): JsonResponse
    {
        $product->product_translations()->delete();
        $product->categories()->detach();
        $product->stocks()->delete();
        $product->taxes()->delete();
        $product->frequently_bought_products()->delete();

        // Optionally: Cart::where('product_id',$product->id)->delete();
        // Optionally: Wishlist::where('product_id',$product->id)->delete();

        $product->delete();

        // Artisan::call('view:clear');
        // Artisan::call('cache:clear');

        return response()->json([
            'message' => 'Product deleted successfully',
        ], 200);
    }
}
