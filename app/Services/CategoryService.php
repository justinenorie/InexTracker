<?php

namespace App\Services;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

class CategoryService
{
    protected $softDeletes;

    protected $transactionService;

    public function __construct(SoftDeleteService $softDeletes, TransactionService $transactionService)
    {
        $this->softDeletes = $softDeletes;
        $this->transactionService = $transactionService;
    }

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
        $user = $category->user;
        $deleted = $category->delete();

        if ($deleted) {
            $this->transactionService->recalculateBalance($user);
        }

        return $deleted;
    }

    /**
     * Get trashed categories for a user.
     */
    public function getTrashedCategoriesForUser(User $user): Collection
    {
        return $this->softDeletes->listTrashedForUser(Category::class, $user);
    }

    /**
     * Restore a trashed category.
     */
    public function restoreCategory(string $id, User $user): bool
    {
        $restored = $this->softDeletes->restoreForUser(Category::class, $id, $user);

        if ($restored) {
            $this->transactionService->recalculateBalance($user);
        }

        return $restored;
    }

    /**
     * Permanently delete a category.
     */
    public function forceDeleteCategory(string $id, User $user): bool
    {
        return $this->softDeletes->forceDeleteForUser(Category::class, $id, $user);
    }
}
