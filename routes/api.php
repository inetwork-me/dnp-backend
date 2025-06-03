<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\ProductController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'v1/auth', 'middleware' => ['app_language']], function () {
    Route::post('login', 'App\Http\Controllers\Api\V1\AuthController@login');
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


Route::group(['prefix' => 'v2', 'middleware' => ['app_language']], function () {
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
    });

    // If you also want “info” to be under v2/auth/info, move it inside the auth‐prefix too:
    Route::group(['prefix' => 'auth'], function () {
        Route::post('info', 'App\Http\Controllers\Api\V1\AuthController@getUserInfoByAccessToken');
    });
});
