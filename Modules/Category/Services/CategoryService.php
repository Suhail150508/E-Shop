<?php

namespace Modules\Category\Services;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Modules\Category\App\Models\Category;

class CategoryService
{
    /**
     * Get all categories with pagination.
     *
     * @param  array|string|null  $filters
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getAll($filters = [])
    {
        // For backward compatibility if $filters is a string (search term)
        if (is_string($filters)) {
            $filters = ['search' => $filters];
        }

        $query = Category::with('parent')->withCount('products')->latest();

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where('name', 'like', "%{$search}%");
        }

        if (isset($filters['status']) && $filters['status'] !== '') {
            $query->where('is_active', (bool) $filters['status']);
        }

        return $query->paginate(10);
    }

    /**
     * Create a new category.
     */
    public function create(array $data): Category
    {
        $data['is_active'] = isset($data['is_active']) ? (bool) $data['is_active'] : false;
        $data['slug'] = Str::slug($data['name']);

        if (isset($data['image']) && $data['image'] instanceof UploadedFile) {
            $data['image'] = $data['image']->store('categories', 'public');
        }

        $category = Category::create($data);
        $this->clearCache();

        return $category;
    }

    /**
     * Update a category.
     */
    public function update(Category $category, array $data): Category
    {
        $data['is_active'] = isset($data['is_active']) ? (bool) $data['is_active'] : $category->is_active;

        if (isset($data['name'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        if (isset($data['image']) && $data['image'] instanceof UploadedFile) {
            if ($category->image) {
                Storage::disk('public')->delete($category->image);
            }
            $data['image'] = $data['image']->store('categories', 'public');
        }

        $category->update($data);
        $this->clearCache();

        return $category;
    }

    /**
     * Delete a category.
     */
    public function delete(Category $category): void
    {
        if ($category->products()->where('is_active', true)->exists()) {
            throw new \Exception(__('Cannot delete category because it has active products.'));
        }

        if ($category->image) {
            Storage::disk('public')->delete($category->image);
        }
        
        $category->delete();
        $this->clearCache();
    }

    /**
     * Bulk delete categories.
     */
    public function bulkDelete(array $ids): void
    {
        $categories = Category::whereIn('id', $ids)->get();
        foreach ($categories as $category) {
            $this->delete($category);
        }
    }

    /**
     * Get the category tree (active parent categories with active children).
     * Cached for 60 minutes.
     */
    public function getTree(): Collection
    {
        return Cache::remember('categories_tree', 60, function () {
            $activeProductCheck = function ($q) {
                $q->where(function ($subQ) {
                    $subQ->whereHas('products', function ($p) {
                        $p->where('is_active', true);
                    })->orWhereHas('subProducts', function ($p) {
                        $p->where('is_active', true);
                    });
                });
            };

            return Category::whereNull('parent_id')
                ->where('is_active', true)
                ->where(function ($q) use ($activeProductCheck) {
                    // Parent has active products
                    $activeProductCheck($q);
                    // OR Parent has active children
                    $q->orWhereHas('children', function ($c) use ($activeProductCheck) {
                        $c->where('is_active', true)
                          ->where($activeProductCheck);
                    });
                })
                ->withCount(['products' => function ($q) {
                    $q->where('is_active', true);
                }])
                ->withCount(['subProducts' => function ($q) {
                    $q->where('is_active', true);
                }])
                ->with(['children' => function ($query) use ($activeProductCheck) {
                    $query->where('is_active', true)
                        ->where($activeProductCheck)
                        ->withCount(['products' => function ($q) {
                            $q->where('is_active', true);
                        }])
                        ->withCount(['subProducts' => function ($q) {
                            $q->where('is_active', true);
                        }]);
                }])
                ->get();
        });
    }

    /**
     * Get featured categories.
     *
     * @param int $limit
     * @return Collection
     */

    public function getFeaturedCategories(int $limit = 8): Collection
    {
        return Cache::remember(
            "featured_categories_{$limit}",
            now()->addMinutes(30),
            function () use ($limit) {
                return Category::query()
                    ->select(['id', 'name', 'slug', 'image'])
                    ->where('is_active', true)
                    ->where(function ($q) {
                        $q->whereHas('products', function ($p) {
                            $p->where('is_active', true);
                        })->orWhereHas('subProducts', function ($p) {
                            $p->where('is_active', true);
                        });
                    })
                    ->withCount(['products' => function ($q) {
                        $q->where('is_active', true);
                    }])
                    ->withCount(['subProducts' => function ($q) {
                        $q->where('is_active', true);
                    }])
                    ->with([
                        'products' => function ($q) {
                            $q->select('id', 'category_id', 'name', 'price', 'image')
                            ->where('is_active', true)
                            ->limit(6);
                        }
                    ])
                    ->limit($limit)
                    ->get();
            }
        );
    }

    /**
     * Get flattened categories for select options.
     */
    public function getFlattenedOptions()
    {
        return Category::where('is_active', true)->pluck('name', 'id');
    }

    protected function clearCache(): void
    {
        Cache::forget('categories_tree');
        Cache::forget('featured_categories');
    }
}
