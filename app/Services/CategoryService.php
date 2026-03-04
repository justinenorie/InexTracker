<?php

namespace App\Services;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

class CategoryService
{
    /**
     * Get all categories for a user.
     */
    public function getCategoriesForUser(User $user): Collection
    {
        return Category::query()
            ->where('user_id', $user->id)
            ->orderBy('type')
            ->orderBy('name')
            ->get();
    }

    /**
     * Create a new category.
     */
    public function createCategory(array $data): Category
    {
        $data['user_id'] = Auth::id();

        return Category::create($data);
    }

    /**
     * Update an existing category.
     */
    public function updateCategory(Category $category, array $data): bool
    {
        return $category->update($data);
    }

    /**
     * Delete a category.
     */
    public function deleteCategory(Category $category): ?bool
    {
        return $category->delete();
    }
}
