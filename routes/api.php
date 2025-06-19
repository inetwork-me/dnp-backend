<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\ProductController;
use Illuminate\Support\Facades\Route;
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
use App\Http\Controllers\Api\V2\ApiFormController;
use App\Http\Controllers\Api\V2\ApiFormFieldController;
use App\Http\Controllers\Api\V2\ApiFormSubmissionController;
use App\Http\Controllers\Api\V2\ApiOrderController;
use App\Http\Controllers\Api\V2\ApiPostTypeCategoriesController;

Route::group(['prefix' => 'v1/auth', 'middleware' => ['app_language']], function () {
    Route::post('login', 'App\Http\Controllers\Api\V1\AuthController@login');
    // TODO 
    // Delete THIS API SECURITY ISSUE
    Route::post('password/change', 'App\Http\Controllers\Api\V1\PasswordResetController@changepassword');
    Route::post('signup', 'App\Http\Controllers\Api\V1\AuthController@signup');
    Route::post('social-login', 'App\Http\Controllers\Api\V1\AuthController@socialLogin');
    Route::post('password/forget_request', 'App\Http\Controllers\Api\V1\PasswordResetController@forgetRequest');
    Route::post('password/confirm_reset', 'App\Http\Controllers\Api\V1\PasswordResetController@confirmReset');
    Route::post('password/resend_code', 'App\Http\Controllers\Api\V1\PasswordResetController@resendCode');
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('logout', 'App\Http\Controllers\Api\V1\AuthController@logout');
        Route::get('account-deletion', 'App\Http\Controllers\Api\V1\AuthController@account_deletion');
        Route::get('user', 'App\Http\Controllers\Api\V1\AuthController@user');
        Route::get('resend_code', 'App\Http\Controllers\Api\V1\AuthController@resendCode');
        Route::post('confirm_code', 'App\Http\Controllers\Api\V1\AuthController@confirmCode');
    });

    Route::post('info', 'App\Http\Controllers\Api\V1\AuthController@getUserInfoByAccessToken');
});

Route::group(['prefix' => 'v1', 'middleware' => ['app_language']], function () {
    Route::get('settings', 'App\Http\Controllers\Api\V1\websiteSettingController@index');
    Route::get('posts/{slug}', 'App\Http\Controllers\Api\V1\WebsitePostsController@show');
    Route::get('post-types/{postType:slug}/posts', 'App\Http\Controllers\Api\V1\WebsitePostsController@indexByType');
    Route::apiResource('post-types/{postType}/categories', ApiPostTypeCategoriesController::class)->only('index');
    Route::apiResource('post-types', ApiPostTypesController::class)->only('show');

    // Route::get('v2/forms/{form:slug}', [ApiFormController::class, 'show']);
    Route::get('forms/slug/{slug}', [ApiFormController::class, 'showBySlug']);

    // Public submission endpoint by slug
    Route::post('forms/slug/{slug}/submit', [ApiFormSubmissionController::class, 'submit']);


    // CART
    Route::get('cart', [WebsiteCartController::class, 'current']);
    Route::post('cart/items', [WebsiteCartController::class, 'addItem']);
    Route::put('cart/items/{item}', [WebsiteCartController::class, 'updateItem']);
    Route::delete('cart/items/{item}', [WebsiteCartController::class, 'removeItem']);

    // ORDERS / CHECKOUT
    Route::post('checkout', [WebsiteOrderController::class, 'store']);
    Route::get('orders', [WebsiteOrderController::class, 'index']);
    Route::get('orders/{order}', [WebsiteOrderController::class, 'show']);


    Route::get('get-search-suggestions', 'App\Http\Controllers\Api\V1\SearchSuggestionController@getList');
    Route::get('languages', 'App\Http\Controllers\Api\V1\LanguageController@getList');
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
    // Route::get('products/category/{id}', 'App\Http\Controllers\Api\V2\ProductController@category')->name('api.products.category');
    Route::get('products/brand/{slug}', 'App\Http\Controllers\Api\V1\ProductController@brand')->name('api.products.brand');

    Route::apiResource('products', 'App\Http\Controllers\Api\V1\ProductController')->except(['store', 'update', 'destroy']);

    Route::get('brands', [BrandController::class, 'index']);
    Route::post('brands', [BrandController::class, 'store']);
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
        Route::get('brands', [BrandController::class, 'index']);
        Route::post('brands', [BrandController::class, 'store']);
        Route::get('brands/{id}', [BrandController::class, 'show']);
        Route::put('brands/{id}', [BrandController::class, 'update']);
        Route::delete('brands/{id}', [BrandController::class, 'destroy']);
        Route::apiResource('coupons', ApiCouponController::class);

        // Route::get('/products',          [ApiProductController::class, 'index']);
        Route::apiResource('products', ApiProductController::class)->only(['index', 'store', 'update', 'destroy']);
        Route::apiResource('products/categories', ApiProductCategoryController::class)->only(['index']);

        Route::get('/products/{product}', [ApiProductController::class, 'show']);
        // Route::post('/products',          [ApiProductController::class, 'store']);
        Route::put('/products/{product}', [ApiProductController::class, 'update']);
        // Route::delete('/products/{product}', [ApiProductController::class, 'destroy']);


        // CMS API 
        Route::apiResource('languages', ApiLanguagesController::class);
        Route::get('/settings', [ApiSettingController::class, 'index']);

        // Update a single setting
        Route::patch('/settings', [ApiSettingController::class, 'update']);

        // Batch update multiple settings
        Route::patch('/settings/batch', [ApiSettingController::class, 'batchUpdate']);


        Route::apiResource('menus', ApiMenuController::class);
        Route::patch('menus/{menu}/default', [ApiMenuController::class, 'setDefault']);

        Route::apiResource('post-types', ApiPostTypesController::class);

        Route::apiResource('posts', ApiPostsController::class);

        // Route::apiResource('post-types/{postType}/categories', ApiPostTypeCategoryController::class);
        Route::apiResource('post-types/{postType}/categories', ApiPostTypeCategoriesController::class);
        // Route::apiResource('post-types/{postType}/categories', ApiPostTypeCategoryController::class);


        Route::apiResource('media', ApiMediaController::class);
        Route::delete('media/bulk', [ApiMediaController::class, 'bulkDestroy']);
        Route::apiResource('folders', ApiFolderController::class);
        Route::post('folders/reorder', [ApiFolderController::class, 'reorder']);
        Route::apiResource('tags', ApiTagController::class);

        Route::apiResource('blocks', ApiBlockController::class);

        Route::apiResource('users', ApiUserController::class);

        Route::prefix('forms')->group(function () {
            Route::get('/', [ApiFormController::class, 'index']);
            Route::post('/', [ApiFormController::class, 'store']);
            Route::get('{form}', [ApiFormController::class, 'show']);
            Route::put('{form}', [ApiFormController::class, 'update']);
            Route::delete('{form}', [ApiFormController::class, 'destroy']);

            Route::post('{form}/fields', [ApiFormFieldController::class, 'store']);
            Route::put('{form}/fields/{field}', [ApiFormFieldController::class, 'update']);
            Route::delete('{form}/fields/{field}', [ApiFormFieldController::class, 'destroy']);

            Route::get('{form}/submissions', [ApiFormSubmissionController::class, 'index']);
        });



        Route::get('orders', [ApiOrderController::class, 'index']);
        Route::get('orders/{order}', [ApiOrderController::class, 'show']);
    });

    // If you also want “info” to be under v2/auth/info, move it inside the auth‐prefix too:
    Route::group(['prefix' => 'auth'], function () {
        Route::post('info', 'App\Http\Controllers\Api\V1\AuthController@getUserInfoByAccessToken');
    });
});
