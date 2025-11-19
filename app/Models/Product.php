<?php

namespace App\Models;

use App;
use App\Models\LoyaltySetting;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{

    protected $guarded = ['choice_attributes'];

    protected $with = ['product_translations', 'taxes'];
    
    protected $appends = ['effective_loyalty_points'];

    protected $casts = [
        'product_service_custom_data' => 'array', // Cast to array for easy manipulation
        'label' => 'array',
        'thumbnail' => 'array',
        'thumbnails' => 'array',
        'desc' => 'array',
        'specs' => 'array',
        'howtouse' => 'array',
        'caution' => 'array',
        'shipping' => 'array',
        'returns' => 'array',
        'is_top_selling' => 'boolean',
        'published' => 'boolean',
        'multimedia' => 'array',
        'is_subscription' => 'boolean',
        'adminstration' => 'array',
        'durationofuse' => 'array',
        'function' => 'array',
        'purposeofuse' => 'array',
        'contraindication' => 'array',
        'requires_branch_selection' => 'boolean',

    ];

    public function packageDetails()
    {
        return $this->hasOne(PackageDetail::class);
    }

    public function scopeTopSelling($query)
    {
        return $query->where('is_top_selling', true);
    }

    public function getTranslation($field = '', $lang = false)
    {
        $lang = $lang == false ? App::getLocale() : $lang;
        $product_translations = $this->product_translations->where('lang', $lang)->first();
        return $product_translations != null ? $product_translations->$field : $this->$field;
    }

    public function bundleItems()
    {
        return $this->belongsToMany(
            Product::class,
            'bundle_product',
            'bundle_id',
            'product_id'
        )->withPivot('quantity');
    }

    public function product_translations()
    {
        return $this->hasMany(ProductTranslation::class);
    }

    public function main_category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'product_categories');
    }

    public function frequently_bought_products()
    {
        return $this->hasMany(FrequentlyBoughtProduct::class);
    }

    public function product_categories()
    {
        return $this->hasMany(ProductCategory::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function orderDetails()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class)->where('status', 1);
    }

    public function product_queries()
    {
        return $this->hasMany(ProductQuery::class);
    }

    public function wishlists()
    {
        return $this->hasMany(Wishlist::class);
    }

    public function stocks()
    {
        return $this->hasMany(ProductStock::class);
    }

    public function taxes()
    {
        return $this->hasMany(ProductTax::class);
    }

    public function specifications()
    {
        return $this->hasMany(ProductSpecification::class);
    }

    public function flash_deal_product()
    {
        return $this->hasOne(FlashDealProduct::class);
    }

    public function bids()
    {
        return $this->hasMany(AuctionProductBid::class);
    }

    /**
     * Branches where this product is available (many-to-many)
     */
    public function branches()
    {
        return $this->belongsToMany(Branch::class, 'product_branch');
    }

    // public function thumbnail()
    // {
    //     return $this->belongsTo(Upload::class, 'thumbnail_img');
    // }

    public function scopePhysical($query)
    {
        return $query->where('digital', 0);
    }

    public function scopeDigital($query)
    {
        return $query->where('digital', 1);
    }

    public function carts()
    {
        return $this->hasMany(Cart::class);
    }

    public function scopeIsApprovedPublished($query)
    {
        return $query->where('approved', '1')->where('published', 1);
    }

    // public function last_viewed_products()
    // {
    //     return $this->hasMany(LastViewedProduct::class);
    // }

    public function loyaltyTransactions()
    {
        return $this->hasMany(LoyaltyPointsTransaction::class);
    }

    // Helper method to get effective loyalty points
    public function getEffectiveLoyaltyPoints()
    {
        // If loyalty_points is explicitly set (even to 0), use that value
        if ($this->loyalty_points !== null) {
            return $this->loyalty_points;
        }

        // Only use default setting if loyalty_points is not set at all
        return LoyaltySetting::get('default_product_points', 0);
    }

    // Check if product awards loyalty points
    public function hasLoyaltyPoints()
    {
        return $this->getEffectiveLoyaltyPoints() > 0;
    }

    // Accessor for appended effective_loyalty_points attribute
    public function getEffectiveLoyaltyPointsAttribute()
    {
        return $this->getEffectiveLoyaltyPoints();
    }

    /**
     * Get stock transactions for this product
     */
    public function stockTransactions()
    {
        return $this->hasMany(ProductStockTransaction::class);
    }
}
