<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\ProductController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V2\ApiBmiSettingController;
use App\Http\Controllers\Api\V2\ApiCouponController;
use App\Http\Controllers\Api\V2\ApiLanguagesController;
use App\Http\Controllers\Api\V2\ApiProductCategoryController;
use App\Http\Controllers\Api\V2\ApiProductController;
use App\Http\Controllers\Api\V2\ApiSettingController;
use App\Http\Controllers\Api\V2\ApiMenuController;
use App\Http\Controllers\Api\V2\ApiPostsController;
use App\Http\Controllers\Api\V2\ApiPostTypesController;
use App\Http\Controllers\Api\V2\ApiMediaController;
use App\Http\Controllers\Api\V2\ApiFolderController;
use App\Http\Controllers\Api\V2\ApiTagController;
use App\Http\Controllers\Api\V2\ApiBlockController;
use App\Http\Controllers\Api\V2\ApiUserController;
use App\Http\Controllers\Api\V1\WebsiteCartController;
use App\Http\Controllers\Api\V1\WebsiteOrderController;
use App\Http\Controllers\Api\V1\WebsiteBmi;
use App\Http\Controllers\Api\V2\ApiFormController;
use App\Http\Controllers\Api\V2\ApiFormFieldController;
use App\Http\Controllers\Api\V2\ApiFormSubmissionController;
use App\Http\Controllers\Api\V2\ApiOrderController;
use App\Http\Controllers\Api\V2\ApiPostTypeCategoriesController;
use App\Http\Controllers\Api\V2\ApiRolesController;
use App\Http\Controllers\Api\V2\ApiPermissionController;
use App\Http\Controllers\Api\V2\ApiRolePermissionController;
use App\Http\Controllers\Api\V2\ApiLoyaltyController;
use App\Http\Controllers\Api\V2\EmailSettingsController;
use App\Http\Controllers\Api\V2\ApiVoucherController;
use App\Http\Controllers\Api\V2\DashboardController;
use App\Http\Controllers\Api\V2\ShippingController;
use App\Http\Controllers\Api\V2\ShipmentController;
use App\Http\Controllers\Api\V2\Admin\ShippingCarrierController;
use App\Http\Controllers\Api\V2\Admin\AdminReviewController;
use App\Http\Controllers\Api\V1\BrandController;

Route::group(['prefix' => 'v1/auth', 'middleware' => ['app_language']], function () {
    Route::post('login', 'App\Http\Controllers\Api\V1\AuthController@login');
    // TODO 
    // Delete THIS API SECURITY ISSUE
    Route::post('password/change', 'App\Http\Controllers\Api\V1\PasswordResetController@changepassword');




    Route::post('signup', 'App\Http\Controllers\Api\V1\AuthController@signup');
    Route::post('social-login', 'App\Http\Controllers\Api\V1\AuthController@socialLogin');
    Route::post('password/forget_request', 'App\Http\Controllers\Api\V1\PasswordResetController@forgetRequest');
    Route::post('password/verify_code', 'App\Http\Controllers\Api\V1\PasswordResetController@verifyCode');
    Route::post('password/confirm_reset', 'App\Http\Controllers\Api\V1\PasswordResetController@confirmReset');
    Route::post('password/resend_code', 'App\Http\Controllers\Api\V1\PasswordResetController@resendCode');
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('logout', 'App\Http\Controllers\Api\V1\AuthController@logout');
        Route::get('account-deletion', 'App\Http\Controllers\Api\V1\AuthController@account_deletion');
        Route::get('user', 'App\Http\Controllers\Api\V1\AuthController@user');
        Route::put('user/profile', 'App\Http\Controllers\Api\V1\AuthController@updateProfile');
        Route::get('resend_code', 'App\Http\Controllers\Api\V1\AuthController@resendCode');
        Route::post('confirm_code', 'App\Http\Controllers\Api\V1\AuthController@confirmCode');
        Route::get('orders', [WebsiteOrderController::class, 'index']);
        Route::get('orders/{order}', [WebsiteOrderController::class, 'show']);

        // Wishlist routes
        Route::get('wishlist', 'App\Http\Controllers\Api\V1\WishlistController@index');
        Route::post('wishlist', 'App\Http\Controllers\Api\V1\WishlistController@store');
        Route::delete('wishlist/{productId}', 'App\Http\Controllers\Api\V1\WishlistController@destroy');
        Route::post('wishlist/sync', 'App\Http\Controllers\Api\V1\WishlistController@sync');
        Route::delete('wishlist', 'App\Http\Controllers\Api\V1\WishlistController@clear');
    });

    Route::post('info', 'App\Http\Controllers\Api\V1\AuthController@getUserInfoByAccessToken');
});

Route::group(['prefix' => 'v1', 'middleware' => ['app_language']], function () {

    Route::apiResource('bmi', WebsiteBmi::class);
    Route::get('bmi/{bmi}/with-settings', [WebsiteBmi::class, 'showWithSettings']);
    Route::get('bmi-settings', [WebsiteBmi::class, 'settings']);

    Route::get('settings', 'App\Http\Controllers\Api\V1\websiteSettingController@index');
    Route::get('posts/{slug}', 'App\Http\Controllers\Api\V1\WebsitePostsController@show');
    Route::get('post-types/{postType:slug}/posts', 'App\Http\Controllers\Api\V1\WebsitePostsController@indexByType');
    Route::apiResource('post-types/{postType}/categories', ApiPostTypeCategoriesController::class)->only('index');
    Route::apiResource('post-types', ApiPostTypesController::class)->only('show');
    Route::apiResource('products/categories', ApiProductCategoryController::class)->only(['index']);

    // Route::get('v2/forms/{form:slug}', [ApiFormController::class, 'show']);
    Route::get('forms/slug/{slug}', [ApiFormController::class, 'showBySlug']);

    // Public submission endpoint by slug
    Route::post('forms/slug/{slug}/submit', [ApiFormSubmissionController::class, 'submit']);


    // CART
    Route::get('cart', [WebsiteCartController::class, 'current']);
    Route::post('cart/items', [WebsiteCartController::class, 'addItem']);
    Route::put('cart/items/{item}', [WebsiteCartController::class, 'updateItem']);
    Route::delete('cart/items/{item}', [WebsiteCartController::class, 'removeItem']);
    // NEW coupon routes
    Route::post('/cart/{cart}/apply-coupon', [WebsiteCartController::class, 'applyCoupon']);
    Route::delete('/cart/{cart}/remove-coupon', [WebsiteCartController::class, 'removeCoupon']);

    // General coupon apply route (for checkout)
    Route::post('coupons/apply', [ApiCouponController::class, 'apply']);

    // ORDERS / CHECKOUT
    Route::post('checkout', [WebsiteOrderController::class, 'store']);
    Route::put('orders/{order}/payment-status', [WebsiteOrderController::class, 'updatePaymentStatus']);

    // SHIPPING - V1 Routes for frontend compatibility
    Route::prefix('shipping')->group(function () {
        Route::post('calculate-rates', [ShippingController::class, 'calculateRates']);
        Route::get('quotes/{cartId}', [ShippingController::class, 'getQuote']);
        Route::post('quotes/{quoteId}/select-method', [ShippingController::class, 'selectMethod']);
        Route::post('validate-address', [ShippingController::class, 'validateAddress']);
        Route::post('delivery-estimate', [ShippingController::class, 'getDeliveryEstimate']);
        Route::get('carriers', [ShippingController::class, 'getAvailableCarriers']);
    });


    Route::middleware('auth:sanctum')->group(function () {

        Route::get('orders', [WebsiteOrderController::class, 'index']);
        Route::get('orders/{order}', [WebsiteOrderController::class, 'show']);
        Route::get('reviews', [WebsiteReviewController::class, 'index']);
        Route::post('reviews', [WebsiteReviewController::class, 'store']);

        // Loyalty endpoints for website
        Route::prefix('loyalty')->group(function () {
            Route::get('summary', [ApiLoyaltyController::class, 'summary']);
            Route::get('transactions', [ApiLoyaltyController::class, 'transactions']);
            Route::post('convert-to-voucher', [ApiLoyaltyController::class, 'convertToVoucher']);
        });

        // Voucher endpoints for website
        Route::prefix('vouchers')->group(function () {
            Route::get('/', [ApiVoucherController::class, 'index']);
            Route::get('active', [ApiVoucherController::class, 'active']);
            Route::post('validate', [ApiVoucherController::class, 'validateVoucher']);
        });
    });

    Route::get('get-search-suggestions', 'App\Http\Controllers\Api\V1\SearchSuggestionController@getList');
    Route::get('languages', 'App\Http\Controllers\Api\V1\LanguageController@getList');
    Route::get('loyalty/settings', [ApiLoyaltyController::class, 'settings']);
    Route::apiResource('banners', 'App\Http\Controllers\Api\V1\BannerController')->only('index');
    Route::get('brands/top', 'App\Http\Controllers\Api\V1\BrandController@top');
    Route::get('all-brands', [ProductController::class, 'getBrands'])->name('allBrands');
    // Route::apiResource('brands', 'App\Http\Controllers\Api\V1\BrandController')->only('index');
    Route::apiResource('business-settings', 'App\Http\Controllers\Api\V1\BusinessSettingController')->only('index');
    Route::get('category/info/{slug}', 'App\Http\Controllers\Api\V1\CategoryController@info');
    Route::get('categories/featured', 'App\Http\Controllers\Api\V1\CategoryController@featured');
    Route::get('categories/home', 'App\Http\Controllers\Api\V1\CategoryController@home');
    Route::get('categories/top', 'App\Http\Controllers\Api\V1\CategoryController@top');
    Route::apiResource('categories', 'App\Http\Controllers\Api\V1\CategoryController')->only('index');
    Route::get('sub-categories/{id}', 'App\Http\Controllers\Api\V1\SubCategoryController@index')->name('subCategories.index');
    Route::apiResource('home-categories', 'App\Http\Controllers\Api\V1\HomeCategoryController')->only('index');

    Route::get('filter/categories', 'App\Http\Controllers\Api\V1\FilterController@categories');
    Route::get('filter/brands', 'App\Http\Controllers\Api\V1\FilterController@brands');

    Route::apiResource('currencies', 'App\Http\Controllers\Api\V1\CurrencyController')->only('index');

    Route::get('products', 'App\Http\Controllers\Api\V1\ProductController@index');
    Route::get('products/random', 'App\Http\Controllers\Api\V1\ProductController@getRandomProducts');
    Route::get('products/{productId}/related', 'App\Http\Controllers\Api\V1\ProductController@getRelatedProducts');
    // Route::get('products/category/{id}', 'App\Http\Controllers\Api\V2\ProductController@category')->name('api.products.category');
    Route::get('products/brand/{slug}', 'App\Http\Controllers\Api\V1\ProductController@brand')->name('api.products.brand');

    Route::apiResource('products', 'App\Http\Controllers\Api\V1\ProductController')->except(['store', 'update', 'destroy']);

    Route::get('brands', [BrandController::class, 'index']);
    Route::post('brands', [BrandController::class, 'store']);
    
    // Public shipment tracking (no authentication required)
    Route::get('shipments/track/{trackingNumber}', [ShipmentController::class, 'trackByNumber']);
});

Route::prefix('v2')->name('api.v2.')->middleware(['app_language'])->group(function () {
    // Now “login” is under “v2/auth”:
    Route::group(['prefix' => 'auth'], function () {
        Route::post('login', 'App\Http\Controllers\Api\V1\AuthController@login');
        Route::post('password/forget_request', 'App\Http\Controllers\Api\V1\PasswordResetController@forgetRequest');
        Route::post('password/confirm_reset', 'App\Http\Controllers\Api\V1\PasswordResetController@confirmReset');
        Route::post('password/resend_code', 'App\Http\Controllers\Api\V1\PasswordResetController@resendCode');
    });

    Route::middleware('auth:sanctum')->group(function () {

        Route::get('/dashboard/overview', [DashboardController::class, 'overview']);

        // Roles & Permissions Management (Admin Only)
        Route::middleware('permission:roles.view|roles.manage')->group(function () {
            Route::get('roles', [ApiRolesController::class, 'index']);
            Route::get('roles/{role}', [ApiRolesController::class, 'show']);
        });
        Route::middleware('permission:roles.manage')->group(function () {
            Route::post('roles', [ApiRolesController::class, 'store']);
            Route::put('roles/{role}', [ApiRolesController::class, 'update']);
            Route::patch('roles/{role}', [ApiRolesController::class, 'update']);
            Route::delete('roles/{role}', [ApiRolesController::class, 'destroy']);
        });

        Route::middleware('permission:permissions.view|permissions.manage')->group(function () {
            Route::get('permissions', [ApiPermissionController::class, 'index']);
            Route::get('permissions/{permission}', [ApiPermissionController::class, 'show']);
        });
        Route::middleware('permission:permissions.manage')->group(function () {
            Route::post('permissions', [ApiPermissionController::class, 'store']);
            Route::post('permissions/bulk', [ApiPermissionController::class, 'bulkStore']);
            Route::put('permissions/{permission}', [ApiPermissionController::class, 'update']);
            Route::patch('permissions/{permission}', [ApiPermissionController::class, 'update']);
            Route::delete('permissions/{permission}', [ApiPermissionController::class, 'destroy']);
        });

        // Role-Permission Assignment
        Route::middleware('permission:roles.manage')->group(function () {
            Route::post('roles/{role}/permissions', [ApiRolePermissionController::class, 'assignPermissionsToRole']);
            Route::get('roles/{role}/permissions', [ApiRolePermissionController::class, 'getRolePermissions']);
            Route::delete('roles/{role}/permissions/{permission}', [ApiRolePermissionController::class, 'removePermissionFromRole']);
        });

        // User-Role Assignment
        Route::middleware('permission:users.manage')->group(function () {
            Route::post('users/{user}/roles', [ApiRolePermissionController::class, 'assignRolesToUser']);
            Route::get('users/{user}/roles', [ApiRolePermissionController::class, 'getUserRoles']);
            Route::delete('users/{user}/roles/{role}', [ApiRolePermissionController::class, 'removeRoleFromUser']);
            Route::post('users/{user}/permissions', [ApiRolePermissionController::class, 'assignPermissionsToUser']);
            Route::get('users/{user}/permissions', [ApiRolePermissionController::class, 'getUserPermissions']);
        });

        // Brands Management
        Route::middleware('permission:brands.view|brands.manage')->group(function () {
            Route::get('brands', [BrandController::class, 'index']);
            Route::get('brands/{id}', [BrandController::class, 'show']);
        });
        Route::middleware('permission:brands.manage')->group(function () {
            Route::post('brands', [BrandController::class, 'store']);
            Route::put('brands/{id}', [BrandController::class, 'update']);
            Route::delete('brands/{id}', [BrandController::class, 'destroy']);
        });

        // Coupons Management
        Route::get('coupons/default-currency', [ApiCouponController::class, 'getDefaultCurrency']);
        Route::middleware('permission:coupons.view|coupons.manage')->group(function () {
            Route::get('coupons', [ApiCouponController::class, 'index']);
            Route::get('coupons/{coupon}', [ApiCouponController::class, 'show']);
            Route::get('coupons/{coupon}/usage', [ApiCouponController::class, 'usage']);
            Route::get('coupons/{coupon}/redemptions', [ApiCouponController::class, 'redemptions']);
        });
        Route::middleware('permission:coupons.manage')->group(function () {
            Route::post('coupons', [ApiCouponController::class, 'store']);
            Route::put('coupons/{coupon}', [ApiCouponController::class, 'update']);
            Route::patch('coupons/{coupon}', [ApiCouponController::class, 'update']);
            Route::delete('coupons/{coupon}', [ApiCouponController::class, 'destroy']);
        });
        Route::post('coupons/apply', [ApiCouponController::class, 'apply']); // No permission needed for applying

        // BMI Settings Management
        Route::middleware('permission:settings.view|settings.manage')->group(function () {
            Route::get('bmi-settings', [ApiBmiSettingController::class, 'index']);
            Route::get('bmi-settings/{bmiSetting}', [ApiBmiSettingController::class, 'show']);
        });
        Route::middleware('permission:settings.manage')->group(function () {
            Route::post('bmi-settings', [ApiBmiSettingController::class, 'store']);
            Route::put('bmi-settings/{bmiSetting}', [ApiBmiSettingController::class, 'update']);
            Route::patch('bmi-settings/{bmiSetting}', [ApiBmiSettingController::class, 'update']);
            Route::delete('bmi-settings/{bmiSetting}', [ApiBmiSettingController::class, 'destroy']);
            Route::post('bmi-settings/{bmiSetting}/toggle-status', [ApiBmiSettingController::class, 'toggleStatus']);
            Route::post('bmi-settings/update-order', [ApiBmiSettingController::class, 'updateOrder']);
        });

        // Products Management
        Route::middleware('permission:products.view|products.manage')->group(function () {
            Route::get('/products', [ApiProductController::class, 'index']);
            Route::get('/products/export', [ApiProductController::class, 'export']);
            Route::get('/products/{product}', [ApiProductController::class, 'show']);
        });
        Route::middleware('permission:products.manage')->group(function () {
            Route::post('/products', [ApiProductController::class, 'store']);
            Route::put('/products/{product}', [ApiProductController::class, 'update']);
            Route::delete('/products/{product}', [ApiProductController::class, 'destroy']);
        });

        // Product Categories Management
        Route::middleware('permission:categories.view|categories.manage')->group(function () {
            Route::get('products/categories', [ApiProductCategoryController::class, 'index']);
            Route::get('products/categories/{category}', [ApiProductCategoryController::class, 'show']);
        });
        Route::middleware('permission:categories.manage')->group(function () {
            Route::post('products/categories', [ApiProductCategoryController::class, 'store']);
            Route::put('products/categories/{category}', [ApiProductCategoryController::class, 'update']);
            Route::patch('products/categories/{category}', [ApiProductCategoryController::class, 'update']);
            Route::delete('products/categories/{category}', [ApiProductCategoryController::class, 'destroy']);
        });

        // CMS - Languages
        Route::middleware('permission:languages.view|languages.manage')->group(function () {
            Route::get('languages', [ApiLanguagesController::class, 'index']);
            Route::get('languages/{language}', [ApiLanguagesController::class, 'show']);
        });
        Route::middleware('permission:languages.manage')->group(function () {
            Route::post('languages', [ApiLanguagesController::class, 'store']);
            Route::put('languages/{language}', [ApiLanguagesController::class, 'update']);
            Route::patch('languages/{language}', [ApiLanguagesController::class, 'update']);
            Route::delete('languages/{language}', [ApiLanguagesController::class, 'destroy']);
        });

        // Settings Management
        Route::middleware('permission:settings.view|settings.manage')->group(function () {
            Route::get('/settings', [ApiSettingController::class, 'index']);
        });
        Route::middleware('permission:settings.manage')->group(function () {
            Route::patch('/settings', [ApiSettingController::class, 'update']);
            Route::patch('/settings/batch', [ApiSettingController::class, 'batchUpdate']);
        });

        // Email Settings
        Route::middleware('permission:settings.view|settings.manage')->prefix('email-settings')->group(function () {
            Route::get('/', [EmailSettingsController::class, 'index']);
        });
        Route::middleware('permission:settings.manage')->prefix('email-settings')->group(function () {
            Route::put('/', [EmailSettingsController::class, 'update']);
            Route::post('/test', [EmailSettingsController::class, 'testEmail']);
        });

        // CMS - Menus
        Route::middleware('permission:menus.view|menus.manage')->group(function () {
            Route::get('menus', [ApiMenuController::class, 'index']);
            Route::get('menus/{menu}', [ApiMenuController::class, 'show']);
        });
        Route::middleware('permission:menus.manage')->group(function () {
            Route::post('menus', [ApiMenuController::class, 'store']);
            Route::put('menus/{menu}', [ApiMenuController::class, 'update']);
            Route::patch('menus/{menu}', [ApiMenuController::class, 'update']);
            Route::delete('menus/{menu}', [ApiMenuController::class, 'destroy']);
            Route::patch('menus/{menu}/default', [ApiMenuController::class, 'setDefault']);
        });

        // CMS - Post Types
        Route::middleware('permission:posts.view|posts.manage')->group(function () {
            Route::get('post-types', [ApiPostTypesController::class, 'index']);
            Route::get('post-types/{postType}', [ApiPostTypesController::class, 'show']);
        });
        Route::middleware('permission:posts.manage')->group(function () {
            Route::post('post-types', [ApiPostTypesController::class, 'store']);
            Route::put('post-types/{postType}', [ApiPostTypesController::class, 'update']);
            Route::patch('post-types/{postType}', [ApiPostTypesController::class, 'update']);
            Route::delete('post-types/{postType}', [ApiPostTypesController::class, 'destroy']);
        });

        // CMS - Posts
        Route::middleware('permission:posts.view|posts.manage')->group(function () {
            Route::get('posts', [ApiPostsController::class, 'index']);
            Route::get('posts/{post}', [ApiPostsController::class, 'show']);
        });
        Route::middleware('permission:posts.manage')->group(function () {
            Route::post('posts', [ApiPostsController::class, 'store']);
            Route::put('posts/{post}', [ApiPostsController::class, 'update']);
            Route::patch('posts/{post}', [ApiPostsController::class, 'update']);
            Route::delete('posts/{post}', [ApiPostsController::class, 'destroy']);
        });

        // CMS - Post Type Categories
        Route::middleware('permission:posts.view|posts.manage')->group(function () {
            Route::get('post-types/{postType}/categories', [ApiPostTypeCategoriesController::class, 'index']);
            Route::get('post-types/{postType}/categories/{category}', [ApiPostTypeCategoriesController::class, 'show']);
        });
        Route::middleware('permission:posts.manage')->group(function () {
            Route::post('post-types/{postType}/categories', [ApiPostTypeCategoriesController::class, 'store']);
            Route::put('post-types/{postType}/categories/{category}', [ApiPostTypeCategoriesController::class, 'update']);
            Route::patch('post-types/{postType}/categories/{category}', [ApiPostTypeCategoriesController::class, 'update']);
            Route::delete('post-types/{postType}/categories/{category}', [ApiPostTypeCategoriesController::class, 'destroy']);
        });

        // CMS - Media Management
        Route::middleware('permission:media.view|media.manage')->group(function () {
            Route::get('media', [ApiMediaController::class, 'index']);
            Route::get('media/{media}', [ApiMediaController::class, 'show']);
        });
        Route::middleware('permission:media.manage')->group(function () {
            Route::post('media', [ApiMediaController::class, 'store']);
            Route::put('media/{media}', [ApiMediaController::class, 'update']);
            Route::patch('media/{media}', [ApiMediaController::class, 'update']);
            Route::delete('media/{media}', [ApiMediaController::class, 'destroy']);
            Route::delete('media/bulk', [ApiMediaController::class, 'bulkDestroy']);
        });

        // CMS - Folders
        Route::middleware('permission:media.view|media.manage')->group(function () {
            Route::get('folders', [ApiFolderController::class, 'index']);
            Route::get('folders/{folder}', [ApiFolderController::class, 'show']);
        });
        Route::middleware('permission:media.manage')->group(function () {
            Route::post('folders', [ApiFolderController::class, 'store']);
            Route::put('folders/{folder}', [ApiFolderController::class, 'update']);
            Route::patch('folders/{folder}', [ApiFolderController::class, 'update']);
            Route::delete('folders/{folder}', [ApiFolderController::class, 'destroy']);
            Route::post('folders/reorder', [ApiFolderController::class, 'reorder']);
        });

        // CMS - Tags
        Route::middleware('permission:posts.view|posts.manage')->group(function () {
            Route::get('tags', [ApiTagController::class, 'index']);
            Route::get('tags/{tag}', [ApiTagController::class, 'show']);
        });
        Route::middleware('permission:posts.manage')->group(function () {
            Route::post('tags', [ApiTagController::class, 'store']);
            Route::put('tags/{tag}', [ApiTagController::class, 'update']);
            Route::patch('tags/{tag}', [ApiTagController::class, 'update']);
            Route::delete('tags/{tag}', [ApiTagController::class, 'destroy']);
        });

        // CMS - Blocks
        Route::middleware('permission:blocks.view|blocks.manage')->group(function () {
            Route::get('blocks', [ApiBlockController::class, 'index']);
            Route::get('blocks/{block}', [ApiBlockController::class, 'show']);
        });
        Route::middleware('permission:blocks.manage')->group(function () {
            Route::post('blocks', [ApiBlockController::class, 'store']);
            Route::put('blocks/{block}', [ApiBlockController::class, 'update']);
            Route::patch('blocks/{block}', [ApiBlockController::class, 'update']);
            Route::delete('blocks/{block}', [ApiBlockController::class, 'destroy']);
        });

        // Users Management
        Route::middleware('permission:users.view|users.manage')->group(function () {
            Route::get('users', [ApiUserController::class, 'index']);
            Route::get('users/{user}', [ApiUserController::class, 'show']);
        });
        Route::middleware('permission:users.manage')->group(function () {
            Route::post('users', [ApiUserController::class, 'store']);
            Route::put('users/{user}', [ApiUserController::class, 'update']);
            Route::patch('users/{user}', [ApiUserController::class, 'update']);
            Route::delete('users/{user}', [ApiUserController::class, 'destroy']);
        });

        // Forms Management
        Route::middleware('permission:forms.view|forms.manage')->prefix('forms')->group(function () {
            Route::get('/', [ApiFormController::class, 'index']);
            Route::get('{form}', [ApiFormController::class, 'show']);
            Route::get('{form}/submissions', [ApiFormSubmissionController::class, 'index']);
        });
        Route::middleware('permission:forms.manage')->prefix('forms')->group(function () {
            Route::post('/', [ApiFormController::class, 'store']);
            Route::put('{form}', [ApiFormController::class, 'update']);
            Route::delete('{form}', [ApiFormController::class, 'destroy']);
            Route::post('{form}/fields', [ApiFormFieldController::class, 'store']);
            Route::put('{form}/fields/{field}', [ApiFormFieldController::class, 'update']);
            Route::delete('{form}/fields/{field}', [ApiFormFieldController::class, 'destroy']);
        });

        // Orders Management
        Route::middleware('permission:orders.view|orders.manage')->group(function () {
            Route::get('orders', [ApiOrderController::class, 'index']);
            Route::get('orders/{order}', [ApiOrderController::class, 'show']);
        });
        Route::middleware('permission:orders.manage')->group(function () {
            Route::put('orders/{order}/status', [ApiOrderController::class, 'updateStatus']);
        });

        // Loyalty System Routes
        Route::middleware('permission:loyalty.view|loyalty.manage')->prefix('loyalty')->group(function () {
            Route::get('summary', [ApiLoyaltyController::class, 'summary']);
            Route::get('transactions', [ApiLoyaltyController::class, 'transactions']);
            Route::get('settings', [ApiLoyaltyController::class, 'settings']);
            Route::get('dashboard-stats', [ApiLoyaltyController::class, 'dashboardStats']);
            Route::get('customers', [ApiLoyaltyController::class, 'customers']);
        });
        Route::middleware('permission:loyalty.manage')->prefix('loyalty')->group(function () {
            Route::post('convert-to-voucher', [ApiLoyaltyController::class, 'convertToVoucher']);
            Route::post('settings', [ApiLoyaltyController::class, 'updateSettings']);
            Route::post('manual-adjustment', [ApiLoyaltyController::class, 'manualAdjustment']);
        });

        // Voucher Routes
        Route::middleware('permission:vouchers.view|vouchers.manage')->prefix('vouchers')->group(function () {
            Route::get('/', [ApiVoucherController::class, 'adminIndex']);
            Route::get('active', [ApiVoucherController::class, 'active']);
            Route::post('validate', [ApiVoucherController::class, 'validateVoucher']);
            Route::get('stats', [ApiVoucherController::class, 'stats']);
        });
        Route::middleware('permission:vouchers.manage')->prefix('vouchers')->group(function () {
            Route::post('/', [ApiVoucherController::class, 'adminStore']);
            Route::put('{voucher}/status', [ApiVoucherController::class, 'updateStatus']);
        });

        // Shipping Routes (General - can be used by order management)
        Route::middleware('permission:shipping.view|shipping.manage|orders.view|orders.manage')->prefix('shipping')->group(function () {
            Route::post('calculate-rates', [ShippingController::class, 'calculateRates']);
            Route::get('quotes/{cartId}', [ShippingController::class, 'getQuote']);
            Route::post('quotes/{quoteId}/select-method', [ShippingController::class, 'selectMethod']);
            Route::post('validate-address', [ShippingController::class, 'validateAddress']);
            Route::post('delivery-estimate', [ShippingController::class, 'getDeliveryEstimate']);
            Route::get('carriers', [ShippingController::class, 'getAvailableCarriers']);
        });

        // Shipment Routes
        Route::middleware('permission:shipping.view|shipping.manage')->prefix('shipments')->group(function () {
            Route::get('/', [ShipmentController::class, 'list']);
            Route::get('{shipment}', [ShipmentController::class, 'show']);
            Route::get('{shipment}/track', [ShipmentController::class, 'track']);
            Route::get('track/{trackingNumber}', [ShipmentController::class, 'trackByNumber']);
            Route::get('{shipment}/label', [ShipmentController::class, 'getLabel']);
        });
        Route::middleware('permission:shipping.manage')->prefix('shipments')->group(function () {
            Route::post('/', [ShipmentController::class, 'create']);
            Route::patch('{shipment}/status', [ShipmentController::class, 'updateStatus']);
        });

        // Admin Shipping Carrier Management
        Route::middleware('permission:shipping.view|shipping.manage')->prefix('admin/shipping')->group(function () {
            Route::get('carriers', [ShippingCarrierController::class, 'index']);
            Route::get('carriers/{carrier}', [ShippingCarrierController::class, 'show']);
            Route::get('supported-carriers', [ShippingCarrierController::class, 'getSupportedCarriers']);
        });
        Route::middleware('permission:shipping.manage')->prefix('admin/shipping')->group(function () {
            Route::post('carriers', [ShippingCarrierController::class, 'store']);
            Route::put('carriers/{carrier}', [ShippingCarrierController::class, 'update']);
            Route::patch('carriers/{carrier}', [ShippingCarrierController::class, 'update']);
            Route::delete('carriers/{carrier}', [ShippingCarrierController::class, 'destroy']);
            Route::post('carriers/{carrier}/test-connection', [ShippingCarrierController::class, 'testConnection']);
            Route::patch('carriers/{carrier}/toggle-status', [ShippingCarrierController::class, 'toggleStatus']);
        });

        // Admin Review Management Routes
        Route::middleware('permission:reviews.view|reviews.manage')->prefix('admin/reviews')->group(function () {
            Route::get('/', [AdminReviewController::class, 'index']);
            Route::get('{review}', [AdminReviewController::class, 'show']);
        });
        Route::middleware('permission:reviews.manage')->prefix('admin/reviews')->group(function () {
            Route::post('/', [AdminReviewController::class, 'store']);
            Route::put('{review}', [AdminReviewController::class, 'update']);
            Route::delete('{review}', [AdminReviewController::class, 'destroy']);
            Route::post('bulk-delete', [AdminReviewController::class, 'bulkDelete']);
            Route::patch('{review}/toggle-status', [AdminReviewController::class, 'toggleStatus']);
        });

        // Get reviews for specific product
        Route::middleware('permission:reviews.view|reviews.manage')->group(function () {
            Route::get('products/{product}/reviews', [AdminReviewController::class, 'getProductReviews']);
        });

        // Business Settings Management
        Route::middleware('permission:settings.view|settings.manage')->group(function () {
            Route::get('business-settings', 'App\Http\Controllers\Api\V1\BusinessSettingController@index');
        });
        Route::middleware('permission:settings.manage')->group(function () {
            Route::post('business-settings/update', 'App\Http\Controllers\BusinessSettingsController@update');
        });
    });

    // If you also want "info" to be under v2/auth/info, move it inside the auth‐prefix too:
    Route::group(['prefix' => 'auth'], function () {
        Route::post('info', 'App\Http\Controllers\Api\V1\AuthController@getUserInfoByAccessToken');
    });
});
