<?php

namespace Modules\Product\Services;

use App\Models\Color;
use App\Models\ProductImage;
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
        
        if (isset($data['slug'])) {
            $data['slug'] = Str::slug($data['slug']);
        } else {
            $data['slug'] = Str::slug($data['name']);
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

        if (isset($data['slug'])) {
            $data['slug'] = Str::slug($data['slug']);
        } elseif (isset($data['name'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        if (isset($data['image']) && $data['image'] instanceof UploadedFile) {
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
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
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }

        foreach ($product->images as $image) {
            Storage::disk('public')->delete($image->path);
            $image->delete();
        }

        $product->delete();
    }

    public function bulkDelete(array $ids)
    {
        $products = Product::whereIn('id', $ids)->get();
        foreach ($products as $product) {
            $this->delete($product);
        }
    }

    public function getFrontendProducts(array $filters)
    {
        $query = Product::with(['category', 'subcategory', 'images', 'brand', 'unit'])
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
}
