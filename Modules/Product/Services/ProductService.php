<?php

namespace Modules\Product\Services;

use App\Models\Color;
use App\Models\ProductImage;
use App\Models\Size;
use App\Models\Unit;
use App\Services\BaseService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Modules\Product\App\Models\Product;

class ProductService extends BaseService
{
    public function getAll(array $filters = [])
    {
        $query = Product::with(['category', 'subcategory', 'brand', 'unit'])->latest();

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if (isset($filters['status']) && $filters['status'] !== '') {
            $query->where('is_active', (bool) $filters['status']);
        }

        if (!empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        if (!empty($filters['brand_id'])) {
            $query->where('brand_id', $filters['brand_id']);
        }

        return $query->paginate(10);
    }

    public function create(array $data)
    {
        $data['is_active'] = isset($data['is_active']) ? (bool) $data['is_active'] : false;
        $data['is_tryable'] = isset($data['is_tryable']) ? (bool) $data['is_tryable'] : false;

        // Ensure foreign keys are null if empty
        foreach (['subcategory_id', 'brand_id', 'unit_id'] as $key) {
            if (isset($data[$key]) && $data[$key] === '') {
                $data[$key] = null;
            }
        }

        if (isset($data['slug'])) {
            $data['slug'] = $this->generateUniqueSlug(Product::class, $data['slug']);
        } else {
            $data['slug'] = $this->generateUniqueSlug(Product::class, $data['name']);
        }

        if (isset($data['image']) && $data['image'] instanceof UploadedFile) {
            $data['image'] = $data['image']->store('products', 'public');
        }

        $galleryImages = $data['gallery_images'] ?? [];
        unset($data['gallery_images']);

        $product = Product::create($data);

        if (is_array($galleryImages)) {
            foreach ($galleryImages as $image) {
                if ($image instanceof UploadedFile) {
                    $path = $image->store('products/gallery', 'public');

                    ProductImage::create([
                        'product_id' => $product->id,
                        'path' => $path,
                        'is_primary' => false,
                    ]);
                }
            }
        }

        return $product;
    }

    public function update(Product $product, array $data)
    {
        $data['is_active'] = isset($data['is_active']) ? (bool) $data['is_active'] : false;
        $data['is_featured'] = isset($data['is_featured']) ? (bool) $data['is_featured'] : false;
        $data['is_flash_sale'] = isset($data['is_flash_sale']) ? (bool) $data['is_flash_sale'] : false;
        $data['is_tryable'] = isset($data['is_tryable']) ? (bool) $data['is_tryable'] : false;

        if (isset($data['slug'])) {
            $data['slug'] = $this->generateUniqueSlug(Product::class, $data['slug'], 'slug', $product->id);
        } elseif (isset($data['name'])) {
            $data['slug'] = $this->generateUniqueSlug(Product::class, $data['name'], 'slug', $product->id);
        }

        if (isset($data['image']) && $data['image'] instanceof UploadedFile) {
            if ($product->image) {
                if (Storage::disk('public')->exists($product->image)) {
                    Storage::disk('public')->delete($product->image);
                } elseif (file_exists(public_path($product->image))) {
                    @unlink(public_path($product->image));
                }
            }
            $data['image'] = $data['image']->store('products', 'public');
        }

        $galleryImages = $data['gallery_images'] ?? [];
        unset($data['gallery_images']);

        $product->update($data);

        if (is_array($galleryImages)) {
            foreach ($galleryImages as $image) {
                if ($image instanceof UploadedFile) {
                    $path = $image->store('products/gallery', 'public');

                    ProductImage::create([
                        'product_id' => $product->id,
                        'path' => $path,
                        'is_primary' => false,
                    ]);
                }
            }
        }

        return $product;
    }

    public function delete(Product $product)
    {
        // Check if product is in any order
        if (\App\Models\OrderItem::where('product_id', $product->id)->exists()) {
             throw new \Exception(__('product::product.error_has_orders'));
        }

        if ($product->image) {
            if (Storage::disk('public')->exists($product->image)) {
                Storage::disk('public')->delete($product->image);
            } elseif (file_exists(public_path($product->image))) {
                @unlink(public_path($product->image));
            }
        }

        foreach ($product->images as $image) {
            if (Storage::disk('public')->exists($image->path)) {
                Storage::disk('public')->delete($image->path);
            } elseif (file_exists(public_path($image->path))) {
                @unlink(public_path($image->path));
            }
            $image->delete();
        }

        $product->delete();
    }

    public function bulkDelete(array $ids)
    {
        $products = Product::whereIn('id', $ids)->with(['images', 'items'])->get(); // items is usually order items relationship if defined, or we check manually
        
        foreach ($products as $product) {
            // Check manually for order items if relationship not defined on Product model
            if (\App\Models\OrderItem::where('product_id', $product->id)->exists()) {
                 throw new \Exception(__('product::product.error_bulk_has_orders', ['name' => $product->name]));
            }
            $this->delete($product);
        }
    }

    public function getFrontendProducts(array $filters)
    {
        $query = Product::with(['category', 'subcategory', 'images', 'brand', 'unit', 'contentTranslations'])
            ->withAvg('approvedReviews', 'rating')
            ->withCount('approvedReviews')
            ->where('is_active', true);

        // Search
        if (!empty($filters['search'])) {
            $query->where(function($q) use ($filters) {
                $q->where('name', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('description', 'like', '%' . $filters['search'] . '%');
            });
        }

        // Categories
        if (!empty($filters['categories'])) {
            $categories = $filters['categories'];
            $query->where(function($q) use ($categories) {
                $q->whereIn('category_id', $categories)
                  ->orWhereIn('subcategory_id', $categories);
            });
        }

        // Colors
        if (!empty($filters['colors'])) {
            $colors = is_array($filters['colors']) ? $filters['colors'] : explode(',', $filters['colors']);
            $query->where(function ($q) use ($colors) {
                foreach ($colors as $color) {
                    $q->orWhereJsonContains('colors', trim($color));
                }
            });
        }

        // Sizes
        if (!empty($filters['sizes'])) {
            $sizes = is_array($filters['sizes']) ? $filters['sizes'] : explode(',', $filters['sizes']);
            $query->where(function ($q) use ($sizes) {
                foreach ($sizes as $size) {
                    $q->orWhereJsonContains('sizes', trim($size));
                }
            });
        }

        // Tags
        if (!empty($filters['tags'])) {
            $tags = is_array($filters['tags']) ? $filters['tags'] : explode(',', $filters['tags']);
            $query->where(function ($q) use ($tags) {
                foreach ($tags as $tag) {
                    $q->orWhereJsonContains('tags', trim($tag));
                }
            });
        }

        // Unit
        if (!empty($filters['unit_id'])) {
            $units = is_array($filters['unit_id']) ? $filters['unit_id'] : explode(',', $filters['unit_id']);
            $query->whereIn('unit_id', $units);
        }

        // Virtual Try-On
        if (!empty($filters['is_tryable'])) {
            $query->where('is_tryable', true);
        }

        // Rating
        if (!empty($filters['rating'])) {
            $query->having('approved_reviews_avg_rating', '>=', (int)$filters['rating']);
        }

        // Price Range
        if (isset($filters['min_price'])) {
            $query->where('price', '>=', $filters['min_price']);
        }
        if (isset($filters['max_price'])) {
            $query->where('price', '<=', $filters['max_price']);
        }

        // Sorting
        switch ($filters['sort']) {
            case 'price_low':
                $query->orderBy('price', 'asc');
                break;
            case 'price_high':
                $query->orderBy('price', 'desc');
                break;
            case 'newest':
                $query->latest();
                break;
            case 'featured':
            default:
                $query->orderBy('is_featured', 'desc')->latest();
                break;
        }

        return $query->paginate(12);
    }

    public function getUniqueColors()
    {
        return Color::where('is_active', true)->orderBy('name')->get();
    }

    public function getUniqueSizes()
    {
        return Size::where('is_active', true)->orderBy('name')->get();
    }

    public function getUniqueUnits()
    {
        return Unit::where('is_active', true)->orderBy('name')->get();
    }

    public function getUniqueTags()
    {
        $tags = Product::where('is_active', true)->whereNotNull('tags')->pluck('tags');
        $uniqueTags = [];
        foreach ($tags as $tagList) {
            if (is_array($tagList)) {
                foreach ($tagList as $tag) {
                    if (!empty(trim($tag))) {
                        $uniqueTags[] = trim($tag);
                    }
                }
            }
        }
        return array_unique($uniqueTags);
    }

    /**
     * Generate a unique slug for a model based on a value.
     *
     * @param string $modelClass Fully-qualified model class
     * @param string $value Value to slugify
     * @param string $field Column to check (default: 'slug')
     * @param int|null $ignoreId Optional ID to ignore (for updates)
     * @return string
     */
    protected function generateUniqueSlug(string $modelClass, string $value, string $field = 'slug', ?int $ignoreId = null): string
    {
        $base = Str::slug($value);
        $slug = $base;
        $i = 1;

        while (true) {
            $model = new $modelClass;
            $query = $model->where($field, $slug);
            if ($ignoreId) {
                $query->where('id', '!=', $ignoreId);
            }

            if (! $query->exists()) {
                break;
            }

            $slug = $base.'-'. $i++;
        }

        return $slug;
    }
}
