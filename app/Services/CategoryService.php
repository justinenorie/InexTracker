<?php

namespace App\Services;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CategoryService
{
    /** @var SoftDeleteService */
    protected $softDeletes;

    /** @var TransactionService */
    protected $transactionService;

    /**
     * @param  SoftDeleteService  $softDeletes  the soft delete service
     * @param  TransactionService  $transactionService  the transaction service
     * @return void
     */
    public function __construct(SoftDeleteService $softDeletes, TransactionService $transactionService)
    {
        $this->softDeletes = $softDeletes;
        $this->transactionService = $transactionService;
    }

    /**
     * Get all categories for a user.
     *
     * @param  User  $user  the user instance
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
     *
     * @param  array  $data  the category data
     */
    public function createCategory(array $data): Category
    {
        $data['user_id'] = Auth::id();

        return Category::create($data);
    }

    /**
     * Update an existing category.
     *
     * @param  Category  $category  the category instance
     * @param  array  $data  the update data
     */
    public function updateCategory(Category $category, array $data): bool
    {
        return $category->update($data);
    }

    /**
     * Delete a category.
     *
     * @param  Category  $category  the category instance
     */
    public function deleteCategory(Category $category): ?bool
    {
        return DB::transaction(function () use ($category) {
            $user = $category->user;
            $deleted = $category->delete();

            if ($deleted) {
                $this->transactionService->recalculateBalance($user);
            }

            return $deleted;
        });
    }

    /**
     * Get trashed categories for a user.
     *
     * @param  User  $user  the user instance
     */
    public function getTrashedCategoriesForUser(User $user): Collection
    {
        return $this->softDeletes->listTrashedForUser(Category::class, $user);
    }

    /**
     * Restore a trashed category.
     *
     * @param  string  $id  the unique id
     * @param  User  $user  the user instance
     */
    public function restoreCategory(string $id, User $user): bool
    {
        return DB::transaction(function () use ($id, $user) {
            $restored = $this->softDeletes->restoreForUser(Category::class, $id, $user);

            if ($restored) {
                $this->transactionService->recalculateBalance($user);
            }

            return $restored;
        });
    }

    /**
     * Permanently delete a category.
     *
     * @param  string  $id  the unique id
     * @param  User  $user  the user instance
     */
    public function forceDeleteCategory(string $id, User $user): bool
    {
        return $this->softDeletes->forceDeleteForUser(Category::class, $id, $user);
    }
}
