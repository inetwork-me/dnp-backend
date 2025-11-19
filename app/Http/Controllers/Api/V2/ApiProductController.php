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
use App\Exports\ProductsExport;
use Maatwebsite\Excel\Facades\Excel;

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
            ->where('type', $request->query('type'))
            ->where('wholesale_product', 0)
            ->when($request->boolean('is_top_selling'), fn ($q) => $q->where('is_top_selling', true));

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
        $product->load(['categories', 'stocks', 'taxes', 'frequently_bought_products', 'bundleItems', 'packageDetails']);
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

        $packageDetails = $payload['package_details'] ?? null;
        unset($payload['package_details']);


        // // 4) Decode the Base64‐encoded thumbnail_img
        // $base64String = $payload['thumbnail_img'];
        // if (!preg_match('/^data:image\/(\w+);base64,/', $base64String, $typeMatch)) {
        //     return response()->json([
        //         'success' => false,
        //         'errors'  => ['Logo must be a valid Base64‐encoded image string.'],
        //     ], 422);
        // }
        // $imageType = strtolower($typeMatch[1]); // e.g. png, jpeg, jpg, gif

        // $base64Data = substr($base64String, strpos($base64String, ',') + 1);
        // $decodedImage = base64_decode($base64Data);
        // if ($decodedImage === false) {
        //     return response()->json([
        //         'success' => false,
        //         'errors'  => ['Failed to decode Base64 image data.'],
        //     ], 422);
        // }

        // // 5) Generate a unique filename and store under public/uploads/brands/
        // $productname = $payload['name'];
        // $filename = $productname . Str::random(10) . '.' . $imageType;
        // $path = 'uploads/products/' . $productname . '/' . $filename;
        // Storage::disk('public')->put($path, $decodedImage);
        // $thumbnail_img = Storage::url($path);
        // $payload['thumbnail_img'] = $thumbnail_img;


        // Extract optional bundle_items
        $bundleItems = $payload['bundle_items'] ?? [];
        unset($payload['bundle_items']);

        // Create product
        $product = $this->productService->store($payload);

        // Handle package-specific details
        if ($product->type === 'package' && $packageDetails) {
            $product->packageDetails()->create([
                'min_months' => $packageDetails['min_months'] ?? 0,
                'min_products' => $packageDetails['min_products'] ?? 0,
                'points' => $packageDetails['points'] ?? 0,
            ]);
        }
        // Handle package-specific details
        if ($product->type === 'package' && $packageDetails) {
            $product->packageDetails()->create([
                'min_months' => $packageDetails['min_months'] ?? 0,
                'min_products' => $packageDetails['min_products'] ?? 0,
                'points' => $packageDetails['points'] ?? 0,
            ]);
        }

        // Attach categories
        if ($request->filled('category_ids')) {
            $product->categories()->attach($request->category_ids);
        }
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
        if (!empty($bundleItems) && $product->type === 'bundle') {
            $sync = [];
            foreach ($bundleItems as $item) {
                $sync[$item['product_id']] = ['quantity' => $item['quantity']];
            }
            $product->bundleItems()->sync($sync);
        }

        $product->load(['categories', 'stocks', 'taxes', 'bundleItems']);

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
            'flash_deal_id', 'flash_discount', 'flash_discount_type',
        ]);

        $packageDetails = $payload['package_details'] ?? null;
        unset($payload['package_details']);


        $bundleItems = $payload['bundle_items'] ?? [];
        unset($payload['bundle_items']);

        $this->productService->update($payload, $product);

        if (!empty($bundleItems) && $product->type === 'bundle') {
            $sync = [];
            foreach ($bundleItems as $item) {
                $sync[$item['product_id']] = ['quantity' => $item['quantity']];
            }
            $product->bundleItems()->sync($sync);
        }

        if ($product->type === 'package' && $packageDetails) {
            $product->packageDetails()->updateOrCreate(
                ['product_id' => $product->id],
                [
                    'min_months' => $packageDetails['min_months'] ?? 0,
                    'min_products' => $packageDetails['min_products'] ?? 0,
                    'points' => $packageDetails['points'] ?? 0,
                ]
            );
        }
        if ($product->type === 'session' && $packageDetails) {
            $product->packageDetails()->updateOrCreate(
                ['product_id' => $product->id],
                [
                    'min_months' => $packageDetails['min_months'] ?? 0,
                    'min_products' => $packageDetails['min_products'] ?? 0,
                    'points' => $packageDetails['points'] ?? 0,
                ]
            );
        }


        if ($product->type === 'session' && $packageDetails) {
            $product->packageDetails()->updateOrCreate(
                ['product_id' => $product->id],
                [
                    'min_months' => $packageDetails['min_months'] ?? 0,
                    'min_products' => $packageDetails['min_products'] ?? 0,
                    'points' => $packageDetails['points'] ?? 0,
                ]
            );
        }
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
        // Check for blocking relationships before deletion
        $blockingReasons = [];

        try {

        // Check cart items
        $cartItemsCount = \App\Models\CartItem::where('product_id', $product->id)->count();
        if ($cartItemsCount > 0) {
            $cartItemsDetails = \App\Models\CartItem::where('product_id', $product->id)
                ->with('cart.user')
                ->get()
                ->map(function ($item) {
                    return [
                        'cart_id' => $item->cart_id,
                        'quantity' => $item->quantity,
                        'user' => $item->cart->user ? $item->cart->user->name : 'Guest',
                        'created_at' => $item->created_at->format('Y-m-d H:i')
                    ];
                });

            $blockingReasons[] = [
                'type' => 'cart_items',
                'count' => $cartItemsCount,
                'message' => "Product is in {$cartItemsCount} active cart(s)",
                'details' => $cartItemsDetails
            ];
        }

        // Check order items
        $orderItemsCount = \App\Models\OrderItem::where('product_id', $product->id)->count();
        if ($orderItemsCount > 0) {
            $orderItems = \App\Models\OrderItem::where('product_id', $product->id)
                ->with('order.user')
                ->latest()
                ->take(10)
                ->get()
                ->map(function ($item) {
                    return [
                        'order_number' => $item->order->order_number ?? 'N/A',
                        'quantity' => $item->quantity,
                        'customer' => $item->order->user->name ?? 'Guest',
                        'order_status' => $item->order->order_status ?? 'unknown',
                        'created_at' => $item->created_at->format('Y-m-d H:i')
                    ];
                });

            $blockingReasons[] = [
                'type' => 'orders',
                'count' => $orderItemsCount,
                'message' => "Product has {$orderItemsCount} order(s)",
                'details' => $orderItems,
                'showing' => min(10, $orderItemsCount)
            ];
        }

        // Check wishlists
        $wishlistsCount = $product->wishlists()->count();
        if ($wishlistsCount > 0) {
            $wishlists = $product->wishlists()
                ->with('user')
                ->get()
                ->map(function ($item) {
                    return [
                        'user' => $item->user->name,
                        'created_at' => $item->created_at->format('Y-m-d H:i')
                    ];
                });

            $blockingReasons[] = [
                'type' => 'wishlists',
                'count' => $wishlistsCount,
                'message' => "Product is in {$wishlistsCount} wishlist(s)",
                'details' => $wishlists
            ];
        }

        // Check reviews
        $reviewsCount = $product->reviews()->count();
        if ($reviewsCount > 0) {
            $reviews = $product->reviews()
                ->with('user')
                ->get()
                ->map(function ($review) {
                    return [
                        'rating' => $review->rating,
                        'user' => $review->user->name ?? 'Guest',
                        'created_at' => $review->created_at->format('Y-m-d H:i')
                    ];
                });

            $blockingReasons[] = [
                'type' => 'reviews',
                'count' => $reviewsCount,
                'message' => "Product has {$reviewsCount} review(s)",
                'details' => $reviews
            ];
        }

        // Check bundle items (products that include this product in a bundle)
        $bundlesCount = \DB::table('bundle_product')
            ->where('product_id', $product->id)
            ->count();
        if ($bundlesCount > 0) {
            $bundles = \DB::table('bundle_product')
                ->join('products', 'bundle_product.bundle_id', '=', 'products.id')
                ->where('bundle_product.product_id', $product->id)
                ->select('products.id', 'products.name', 'bundle_product.quantity')
                ->get();

            $blockingReasons[] = [
                'type' => 'bundles',
                'count' => $bundlesCount,
                'message' => "Product is included in {$bundlesCount} bundle(s)",
                'details' => $bundles->map(function ($bundle) {
                    return [
                        'bundle_id' => $bundle->id,
                        'bundle_name' => $bundle->name,
                        'quantity' => $bundle->quantity
                    ];
                })
            ];
        }

        } catch (\Exception $e) {
            // If checking relationships fails, log the error and continue with safe deletion attempt
            \Log::error('Error checking product relationships for deletion', [
                'product_id' => $product->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }

        // If there are blocking reasons, return detailed error
        if (!empty($blockingReasons)) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete product due to existing relationships',
                'blocking_reasons' => $blockingReasons,
                'total_blocks' => count($blockingReasons),
                'product' => [
                    'id' => $product->id,
                    'name' => $product->name,
                    'type' => $product->type
                ]
            ], 422);
        }

        // If no blocking reasons, proceed with deletion
        try {
            DB::beginTransaction();

            $product->product_translations()->delete();
            $product->categories()->detach();
            $product->stocks()->delete();
            $product->taxes()->delete();
            $product->frequently_bought_products()->delete();

            $product->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Product deleted successfully',
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to delete product',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * POST /api/products/{product}/remove-from-carts
     * Remove product from all active carts and notify users
     */
    public function removeFromCarts(Product $product): JsonResponse
    {
        try {
            DB::beginTransaction();

            // Get all cart items for this product with user information
            $cartItems = \App\Models\CartItem::where('product_id', $product->id)
                ->with(['cart.user'])
                ->get();

            if ($cartItems->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Product is not in any carts',
                ], 404);
            }

            $affectedUsers = [];
            $guestCarts = 0;
            $removedCount = 0;

            foreach ($cartItems as $cartItem) {
                $cart = $cartItem->cart;

                // Track affected users
                if ($cart->user) {
                    $affectedUsers[] = [
                        'id' => $cart->user->id,
                        'name' => $cart->user->name,
                        'email' => $cart->user->email,
                        'quantity' => $cartItem->quantity,
                    ];
                } else {
                    $guestCarts++;
                }

                // Remove the cart item
                $cartItem->delete();
                $removedCount++;
            }

            // Send notifications to affected users
            foreach (collect($affectedUsers)->unique('id') as $user) {
                // Log notification for now (you can implement email/push notifications later)
                \Log::info('Product removed from cart - User notification', [
                    'user_id' => $user['id'],
                    'user_name' => $user['name'],
                    'user_email' => $user['email'],
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'quantity' => $user['quantity'],
                    'message' => "The product '{$product->name}' has been removed from your cart as it is no longer available."
                ]);

                // TODO: Implement actual notifications
                // Option 1: Create in-app notification if you have a notifications table
                // Option 2: Send email notification
                // Mail::to($user['email'])->send(new ProductRemovedFromCart($product, $user));
                // Option 3: Send push notification
                // Option 4: Use Laravel Notifications
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Product removed from {$removedCount} cart(s)",
                'data' => [
                    'removed_count' => $removedCount,
                    'affected_users' => count(collect($affectedUsers)->unique('id')),
                    'guest_carts' => $guestCarts,
                    'users' => collect($affectedUsers)->unique('id')->values(),
                ]
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to remove product from carts',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * GET /api/products/export
     * Export all products to Excel
     */
    public function export()
    {
        return Excel::download(new ProductsExport, 'products.xlsx');
    }
}
