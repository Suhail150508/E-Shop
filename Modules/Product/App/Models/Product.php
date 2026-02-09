<?php

namespace Modules\Product\App\Models;

use App\Models\Brand;
use App\Models\ProductImage;
use App\Models\ProductReview;
use App\Traits\HasContentTranslations;
use App\Models\Unit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Category\App\Models\Category;

class Product extends Model
{
    use HasContentTranslations;

    protected $fillable = [
        'category_id',
        'subcategory_id',
        'brand_id',
        'unit_id',
        'name',
        'sku',
        'slug',
        'description',
        'price',
        'discount_price',
        'stock',
        'weight',
        'dimensions',
        'image',
        'is_active',
        'is_featured',
        'is_flash_sale',
        'is_tryable',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'colors',
        'sizes',
        'tags',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'discount_price' => 'decimal:2',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'is_flash_sale' => 'boolean',
        'is_tryable' => 'boolean',
        'colors' => 'array',
        'sizes' => 'array',
        'tags' => 'array',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function subcategory(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'subcategory_id');
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(ProductReview::class);
    }

    public function approvedReviews(): HasMany
    {
        return $this->hasMany(ProductReview::class)->where('is_approved', true);
    }

    /**
     * Get the Color models associated with the product.
     * Assumes 'colors' attribute contains an array of color names.
     */
    public function getColorObjectsAttribute()
    {
        if (empty($this->colors) || !is_array($this->colors)) {
            return collect([]);
        }
        return \App\Models\Color::whereIn('name', $this->colors)->get();
    }

    /**
     * Get the Size models associated with the product.
     * Assumes 'sizes' attribute contains an array of size names.
     */
    public function getSizeObjectsAttribute()
    {
        if (empty($this->sizes) || !is_array($this->sizes)) {
            return collect([]);
        }
        return \App\Models\Size::whereIn('name', $this->sizes)->get();
    }

    public function getImageUrlAttribute()
    {
        return getImageOrPlaceholder($this->image, '600x600');
    }

    public function getFinalPriceAttribute()
    {
        if ($this->discount_price > 0 && $this->discount_price < $this->price) {
            return $this->discount_price;
        }
        return $this->price;
    }

    public function getHasDiscountAttribute()
    {
        return $this->discount_price > 0 && $this->discount_price < $this->price;
    }

    public function getDiscountPercentageAttribute()
    {
        if (!$this->has_discount) {
            return 0;
        }
        return round((($this->price - $this->discount_price) / $this->price) * 100);
    }
}
