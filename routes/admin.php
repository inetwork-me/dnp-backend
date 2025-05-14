<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AizUploadController;
use App\Http\Controllers\AttributeController;
use App\Http\Controllers\BlogCategoryController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\BusinessSettingsController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CityController;
use App\Http\Controllers\CMS\HomeController;
use App\Http\Controllers\CMS\PostController;
use App\Http\Controllers\CMS\PostTypeController;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\CurrencyController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\MainSettingsController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RecipeCategoryController;
use App\Http\Controllers\RecipeController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\StateController;
use App\Http\Controllers\WebsiteController;
use App\Http\Controllers\ZoneController;

Route::controller(AizUploadController::class)->group(function () {
    Route::post('/aiz-uploader', 'show_uploader');
    Route::post('/aiz-uploader/upload', 'upload');
    Route::get('/aiz-uploader/get-uploaded-files', 'get_uploaded_files');
    Route::post('/aiz-uploader/get_file_by_ids', 'get_preview_files');
    Route::get('/aiz-uploader/download/{id}', 'attachment_download')->name('download_attachment');
});

Route::post('/language', [LanguageController::class, 'changeLanguage'])->name('language.change');

Route::get('/admin', [AdminController::class, 'admin_dashboard'])->name('admin.dashboard')->middleware(['admin', 'prevent-back-history']);
Route::group(['prefix' => 'admin', 'middleware' => ['auth', 'admin', 'prevent-back-history']], function () {

    // category
    Route::resource('categories', CategoryController::class);
    Route::controller(CategoryController::class)->group(function () {
        Route::get('/categories/edit/{id}', 'edit')->name('categories.edit');
        Route::get('/categories/destroy/{id}', 'destroy')->name('categories.destroy');
        Route::post('/categories/featured', 'updateFeatured')->name('categories.featured');
        Route::post('/categories/categoriesByType', 'categoriesByType')->name('categories.categories-by-type');

        // category-wise discount set
        Route::get('/categories-wise-product-discount', 'categoriesWiseProductDiscount')->name('categories_wise_product_discount');
    });

    // attributes
    Route::resource('attributes', AttributeController::class);
    Route::controller(AttributeController::class)->group(function () {
        Route::get('/attributes/edit/{id}', 'edit')->name('attributes.edit');
        Route::get('/attributes/destroy/{id}', 'destroy')->name('attributes.destroy');

        //Attribute Value
        Route::post('/store-attribute-value', 'store_attribute_value')->name('store-attribute-value');
        Route::get('/edit-attribute-value/{id}', 'edit_attribute_value')->name('edit-attribute-value');
        Route::post('/update-attribute-value/{id}', 'update_attribute_value')->name('update-attribute-value');
        Route::get('/destroy-attribute-value/{id}', 'destroy_attribute_value')->name('destroy-attribute-value');

        //Colors
        Route::get('/colors', 'colors')->name('colors');
        Route::post('/colors/store', 'store_color')->name('colors.store');
        Route::get('/colors/edit/{id}', 'edit_color')->name('colors.edit');
        Route::post('/colors/update/{id}', 'update_color')->name('colors.update');
        Route::get('/colors/destroy/{id}', 'destroy_color')->name('colors.destroy');
    });

    // Brands
    Route::resource('brands', BrandController::class);
    Route::controller(BrandController::class)->group(function () {
        Route::get('/brands/edit/{id}', 'edit')->name('brands.edit');
        Route::get('/brands/destroy/{id}', 'destroy')->name('brands.destroy');
        Route::get('/brand-bulk-upload', 'upload')->name('brand_bulk_upload.index');
        Route::post('/brand-bulk-upload/store', 'bulk_upload')->name('brand_bulk_upload');
    });

    // products
    Route::controller(ProductController::class)->group(function () {
        Route::get('/products/admin', 'admin_products')->name('products.admin');
        Route::get('/products/seller/{product_type}', 'seller_products')->name('products.seller');
        Route::get('/products/all', 'all_products')->name('products.all');
        Route::get('/products/create', 'create')->name('products.create');
        Route::post('/products/store/', 'store')->name('products.store');
        Route::get('/products/admin/{id}/edit', 'admin_product_edit')->name('products.admin.edit');
        Route::get('/products/seller/{id}/edit', 'seller_product_edit')->name('products.seller.edit');
        Route::post('/products/update/{product}', 'update')->name('products.update');
        Route::post('/products/todays_deal', 'updateTodaysDeal')->name('products.todays_deal');
        Route::post('/products/featured', 'updateFeatured')->name('products.featured');
        Route::post('/products/published', 'updatePublished')->name('products.published');
        Route::post('/products/approved', 'updateProductApproval')->name('products.approved');
        Route::post('/products/get_products_by_subcategory', 'get_products_by_subcategory')->name('products.get_products_by_subcategory');
        Route::get('/products/duplicate/{id}', 'duplicate')->name('products.duplicate');
        Route::get('/products/destroy/{id}', 'destroy')->name('products.destroy');
        Route::post('/bulk-product-delete', 'bulk_product_delete')->name('bulk-product-delete');

        Route::post('/products/sku_combination', 'sku_combination')->name('products.sku_combination');
        Route::post('/products/sku_combination_edit', 'sku_combination_edit')->name('products.sku_combination_edit');
        Route::post('/products/add-more-choice-option', 'add_more_choice_option')->name('products.add-more-choice-option');
        Route::post('/product-search', 'product_search')->name('product.search');
        Route::post('/get-selected-products', 'get_selected_products')->name('get-selected-products');
        Route::post('/set-product-discount', 'setProductDiscount')->name('set_product_discount');

        Route::get('/products-make-vats', 'makeVat')->name('vats.make.products');
        Route::post('/products-update-vats', 'updateVate')->name('vats.update.products');

        Route::get('/products-make-commission', 'makeCommission')->name('commission.make.products');
        Route::post('/products-update-commission', 'updateCommission')->name('commission.update.products');

        Route::resource('blog-category', BlogCategoryController::class);
        Route::get('/blog-category/destroy/{id}', [BlogCategoryController::class, 'destroy'])->name('blog-category.destroy');

        // Blog
        Route::resource('blog', BlogController::class);
        Route::controller(BlogController::class)->group(function () {
            Route::get('/blog/edit/{id}', 'edit')->name('blog.edit');
            Route::get('/blog/destroy/{id}', 'destroy')->name('blog.destroy');
            Route::post('/blog/change-status', 'change_status')->name('blog.change-status');
        });

        // Recipes
        Route::resource('recipe-category', RecipeCategoryController::class);
        Route::get('/recipe-category/destroy/{id}', [RecipeCategoryController::class, 'destroy'])->name('recipe-category.destroy');

        Route::resource('recipe', RecipeController::class);
        Route::controller(RecipeController::class)->group(function () {
            Route::get('/recipe/edit/{id}', 'edit')->name('recipe.edit');
            Route::get('/recipe/destroy/{id}', 'destroy')->name('recipe.destroy');
            Route::post('/recipe/change-status', 'change_status')->name('recipe.change-status');
        });
    });


    // Staff
    Route::resource('staffs', StaffController::class);
    Route::get('/staffs/destroy/{id}', [StaffController::class, 'destroy'])->name('staffs.destroy');

    // Roles
    Route::resource('roles', RoleController::class);
    Route::controller(RoleController::class)->group(function () {
        Route::get('/roles/edit/{id}', 'edit')->name('roles.edit');
        Route::get('/roles/destroy/{id}', 'destroy')->name('roles.destroy');

        // Add Permissiom
        Route::post('/roles/add_permission', 'add_permission')->name('roles.permission');
    });



    // Main Settings
    Route::controller(MainSettingsController::class)->group(function () {
        Route::post('/main-settings/update', 'update')->name('main_settings.update');
        Route::post('/main-settings/update/activation', 'updateActivationSettings')->name('main_settings.update.activation');
        Route::post('/payment-activation', 'updatePaymentActivationSettings')->name('payment.activation');
        Route::get('/general-setting', 'general_setting')->name('general_setting.index');
        Route::get('/activation', 'activation')->name('activation.index');
        Route::get('/payment-method', 'payment_method')->name('payment_method.index');
        Route::get('/file_system', 'file_system')->name('file_system.index');
        Route::get('/social-login', 'social_login')->name('social_login.index');
        Route::get('/smtp-settings', 'smtp_settings')->name('smtp_settings.index');
        Route::get('/google-analytics', 'google_analytics')->name('google_analytics.index');
        Route::get('/google-recaptcha', 'google_recaptcha')->name('google_recaptcha.index');
        Route::get('/google-map', 'google_map')->name('google-map.index');
        Route::get('/google-firebase', 'google_firebase')->name('google-firebase.index');
        Route::get('/facebook-chat', 'facebook_chat')->name('facebook_chat.index');
        Route::post('/facebook_chat', 'facebook_chat_update')->name('facebook_chat.update');
        Route::get('/facebook-comment', 'facebook_comment')->name('facebook-comment');
        Route::post('/facebook-comment', 'facebook_comment_update')->name('facebook-comment.update');
        Route::post('/facebook_pixel', 'facebook_pixel_update')->name('facebook_pixel.update');
        Route::post('/env_key_update', 'env_key_update')->name('env_key_update.update');
        Route::post('/payment_method_update', 'payment_method_update')->name('payment_method.update');
        Route::post('/google_analytics', 'google_analytics_update')->name('google_analytics.update');
        Route::post('/google_recaptcha', 'google_recaptcha_update')->name('google_recaptcha.update');
        Route::post('/google-map', 'google_map_update')->name('google-map.update');
        Route::post('/google-firebase', 'google_firebase_update')->name('google-firebase.update');
        Route::get('/verification/form', 'seller_verification_form')->name('seller_verification_form.index');
        Route::post('/verification/form', 'seller_verification_form_update')->name('seller_verification_form.update');
        Route::get('/vendor_commission', 'vendor_commission')->name('business_settings.vendor_commission');
        Route::post('/vendor_commission_update', 'vendor_commission_update')->name('business_settings.vendor_commission.update');
        Route::get('/shipping_configuration', 'shipping_configuration')->name('shipping_configuration.index');
        Route::post('/shipping_configuration/update', 'shipping_configuration_update')->name('shipping_configuration.update');
        Route::get('/order-configuration', 'order_configuration')->name('order_configuration.index');
    });

    Route::get('/all-notification', [NotificationController::class, 'index'])->name('admin.all-notification');

    Route::get('/clear-cache', [AdminController::class, 'clearCache'])->name('cache.clear');

    Route::get('/admin-permissions', [RoleController::class, 'create_admin_permissions']);

    Route::resource('profile', ProfileController::class);


    // Business Settings
    Route::controller(BusinessSettingsController::class)->group(function () {
        Route::post('/business-settings/update', 'update')->name('business_settings.update');
        Route::post('/business-settings/update/activation', 'updateActivationSettings')->name('business_settings.update.activation');
        Route::post('/payment-activation', 'updatePaymentActivationSettings')->name('payment.activation');
        Route::get('/general-setting', 'general_setting')->name('general_setting.index');
        Route::get('/file_system', 'file_system')->name('file_system.index');
    });

    Route::controller(WebsiteController::class)->group(function () {
        Route::get('/footer', 'footer')->name('website.footer');
        Route::get('/header', 'header')->name('website.header');
        Route::get('/appearance', 'appearance')->name('website.appearance');
        Route::get('/select-homepage', 'select_homepage')->name('website.select-homepage');
        Route::get('/authentication-layout-settings', 'authentication_layout_settings')->name('website.authentication-layout-settings');
        Route::get('/pages', 'pages')->name('website.pages');
    });

    // Custom Page
    Route::resource('custom-pages', PageController::class);
    Route::controller(PageController::class)->group(function () {
        Route::get('/custom-pages/sections/{id}', 'sections')->name('custom-pages.sections');
        Route::get('/custom-pages/section-create/{id}', 'sectionCreate')->name('custom-pages.sectionCreate');
        Route::post('/custom-pages/section-store/{id}', 'sectionStore')->name('custom-pages.sectionStore');
        Route::get('/custom-pages/section-delete/{id}', 'sectionDelete')->name('custom-pages.sectionDelete');
        Route::post('/custom-pages/section-update/{id}', 'sectionUpdate')->name('custom-pages.sectionUpdate');
        Route::get('/custom-pages/edit/{id}', 'edit')->name('custom-pages.edit');
        Route::get('/custom-pages/destroy/{id}', 'destroy')->name('custom-pages.destroy');
    });

    //Currency
    Route::controller(CurrencyController::class)->group(function () {
        Route::get('/currency', 'currency')->name('currency.index');
        Route::get('/currency/create', 'create')->name('currency.create');
        Route::post('/currency/store', 'store')->name('currency.store');
        Route::get('/currency/edit/{id}', 'edit')->name('currency.edit');
        Route::post('/currency/update/{id}', 'updateCurrency')->name('currency.update');
        Route::post('/currency/update_status', 'update_status')->name('currency.update_status');
        Route::get('/currency/destroy/{id}', 'destroy')->name('currency.destroy');
    });

    // Language
    Route::resource('/languages', LanguageController::class);
    Route::controller(LanguageController::class)->group(function () {
        Route::post('/languages/{id}/update', 'update')->name('languages.update');
        Route::get('/languages/destroy/{id}', 'destroy')->name('languages.destroy');
        Route::post('/languages/update_rtl_status', 'update_rtl_status')->name('languages.update_rtl_status');
        Route::post('/languages/update-status', 'update_status')->name('languages.update-status');
        Route::post('/languages/key_value_store', 'key_value_store')->name('languages.key_value_store');

        //Frontend Trasnlation
        Route::post('/languages/app-translations/import', 'importEnglishFile')->name('app-translations.import');
        Route::get('/languages/app-translations/show/{id}', 'showAppTranlsationView')->name('app-translations.show');
        Route::post('/languages/app-translations/key_value_store', 'storeAppTranlsation')->name('app-translations.store');
        Route::get('/languages/app-translations/export/{id}', 'exportARBFile')->name('app-translations.export');
    });

    // Countries
    Route::resource('countries', CountryController::class);
    Route::controller(CountryController::class)->group(function () {
        Route::get('/countries/edit/{id}', 'edit')->name('countries.edit');
        Route::get('/countries/destroy/{id}', 'destroy')->name('countries.destroy');
        Route::post('/countries/status', 'updateStatus')->name('countries.status');
    });

    // States
    Route::resource('states', StateController::class);
    Route::post('/states/status', [StateController::class, 'updateStatus'])->name('states.status');

    // Zones
    Route::resource('zones', ZoneController::class);
    Route::get('/zones/destroy/{id}', [ZoneController::class, 'destroy'])->name('zones.destroy');

    // Cities
    Route::resource('cities', CityController::class);
    Route::controller(CityController::class)->group(function () {
        Route::get('/cities/edit/{id}', 'edit')->name('cities.edit');
        Route::get('/cities/destroy/{id}', 'destroy')->name('cities.destroy');
        Route::post('/cities/status', 'updateStatus')->name('cities.status');
    });

    // CMS
    Route::group(['prefix' => 'cms', 'as' => 'cms.'], function () {
        Route::get('/', [HomeController::class,'index'])->name('home');
        
        // Custom Post Types
        Route::resource('cpt', PostTypeController::class);
        Route::controller(PostTypeController::class)->group(function () {
            Route::get('/cpt/edit/{id}', 'edit')->name('cpt.edit');
            Route::get('/cpt/destroy/{id}', 'index')->name('cpt.destroy');
        });
        
        // Posts
        Route::resource('posts', PostController::class);
        Route::controller(PostController::class)->group(function () {
            Route::get('/posts/edit/{id}', 'edit')->name('posts.edit');
            Route::get('/posts/destroy/{id}', 'index')->name('posts.destroy');
        });

        // ACF
        Route::resource('field-groups', PostTypeController::class);
        Route::controller(PostTypeController::class)->group(function () {
            Route::get('/field-groups/edit/{id}', 'edit')->name('field-groups.edit');
            Route::get('/field-groups/destroy/{id}', 'index')->name('field-groups.destroy');
        });
    });
});
