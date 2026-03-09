<?php

namespace App\Http\Controllers;

use App\Http\Requests\Category\StoreCategoryRequest;
use App\Http\Requests\Category\UpdateCategoryRequest;
use App\Models\Category;
use App\Services\CategoryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CategoryController extends Controller
{
    /** @var CategoryService */
    protected $service;

    /**
     * @param  CategoryService  $service the category service instance
     * @return void
     */
    public function __construct(CategoryService $service)
    {
        $this->service = $service;
    }

    /**
     * Display a listing of categories.
     *
     * @param  Request  $request the incoming request
     * @return Response
     */
    public function index(Request $request): Response
    {
        $user = $request->user();

        $categories = $this->service->getCategoriesForUser($user);

        return Inertia::render('categories/Index', [
            'categories' => $categories,
        ]);
    }

    /**
     * Store a new category.
     *
     * @param  StoreCategoryRequest  $request the store request
     * @return RedirectResponse
     */
    public function store(StoreCategoryRequest $request): RedirectResponse
    {
        $this->service->createCategory($request->validated());

        return back();
    }

    /**
     * Update an existing category.
     *
     * @param  UpdateCategoryRequest  $request the update request
     * @param  Category  $category the category instance
     * @return RedirectResponse
     */
    public function update(UpdateCategoryRequest $request, Category $category): RedirectResponse
    {
        $this->service->updateCategory($category, $request->validated());

        return back();
    }

    /**
     * Delete an existing category.
     *
     * @param  Category  $category the category instance
     * @return RedirectResponse
     */
    public function destroy(Category $category): RedirectResponse
    {
        $this->service->deleteCategory($category);

        return back();
    }

    /**
     * Restore a trashed category.
     *
     * @param  Request  $request the incoming request
     * @param  string  $id the category unique id
     * @return RedirectResponse
     */
    public function restore(Request $request, string $id): RedirectResponse
    {
        $this->service->restoreCategory($id, $request->user());

        return back()->with('success', 'Category restored successfully!');
    }

    /**
     * Permanently delete a trashed category.
     *
     * @param  Request  $request the incoming request
     * @param  string  $id the category unique id
     * @return RedirectResponse
     */
    public function forceDelete(Request $request, string $id): RedirectResponse
    {
        $this->service->forceDeleteCategory($id, $request->user());

        return back()->with('success', 'Category permanently deleted.');
    }
}
