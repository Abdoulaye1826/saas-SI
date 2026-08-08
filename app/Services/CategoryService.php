<?php

namespace App\Services;

use App\Models\Category;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Logique métier des catégories produits.
 */
class CategoryService
{
    public function __construct(
        private readonly ActivityLogService $activityLog
    ) {}

    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return Category::query()
            ->withCount('products')
            ->when($filters['search'] ?? null, function ($q, $search) {
                $q->where(function ($q2) use ($search) {
                    $q2->where('name', 'like', "%{$search}%")
                        ->orWhere('slug', 'like', "%{$search}%");
                });
            })
            ->when(isset($filters['is_active']) && $filters['is_active'] !== '', function ($q) use ($filters) {
                $q->where('is_active', (bool) $filters['is_active']);
            })
            ->orderBy('name')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function summary(): array
    {
        return [
            'total' => Category::count(),
            'active' => Category::where('is_active', true)->count(),
            'inactive' => Category::where('is_active', false)->count(),
        ];
    }

    public function create(array $data, ?UploadedFile $image = null): Category
    {
        $data['slug'] = Str::slug($data['name']);

        if ($image) {
            $data['image_path'] = $this->storeImage($image);
        }

        $category = Category::create($data);

        $this->activityLog->log('create', $category, "Catégorie créée : {$category->name}");

        return $category;
    }

    public function update(Category $category, array $data, ?UploadedFile $image = null, bool $removeImage = false): Category
    {
        $data['slug'] = Str::slug($data['name']);

        if ($removeImage && $category->image_path) {
            $this->deleteImage($category->image_path);
            $data['image_path'] = null;
        }

        if ($image) {
            if ($category->image_path) {
                $this->deleteImage($category->image_path);
            }
            $data['image_path'] = $this->storeImage($image);
        }

        $category->update($data);

        $this->activityLog->log('update', $category, "Catégorie modifiée : {$category->name}");

        return $category->fresh();
    }

    private function storeImage(UploadedFile $image): string
    {
        return $image->store('categories', 'public');
    }

    private function deleteImage(string $path): void
    {
        Storage::disk('public')->delete($path);
    }

    public function delete(Category $category): void
    {
        if ($category->products()->exists()) {
            throw new \RuntimeException('Impossible de supprimer une catégorie contenant des produits.');
        }

        $name = $category->name;
        $category->delete();

        $this->activityLog->log('delete', null, "Catégorie supprimée : {$name}");
    }

    /** @return \Illuminate\Database\Eloquent\Collection<int, Category> */
    public function activeList()
    {
        return Category::active()->orderBy('name')->get();
    }
}
