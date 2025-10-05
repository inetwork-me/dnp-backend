<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Branch extends Model
{
    protected $fillable = [
        'name',
        'code',
        'address',
        'city',
        'phone',
        'email',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'name' => 'array', // JSON: {"en": "Branch Name", "ar": "اسم الفرع"}
        'is_active' => 'boolean',
    ];

    /**
     * Get name by locale (like Product label/desc pattern)
     */
    public function getName($locale = 'en')
    {
        return $this->name[$locale] ?? $this->name['en'] ?? '';
    }

    /**
     * Products available at this branch
     */
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'product_branch');
    }

    /**
     * Order items for this branch
     */
    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Scope for active branches
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('sort_order');
    }
}
