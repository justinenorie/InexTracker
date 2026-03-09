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
    protected $service;

    public function __construct(CategoryService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request): Response
    {
        $user = $request->user();

        $categories = $this->service->getCategoriesForUser($user);

        return Inertia::render('categories/Index', [
            'categories' => $categories,
        ]);
    }

    public function store(StoreCategoryRequest $request): RedirectResponse
    {
        $this->service->createCategory($request->validated());

        return back();
    }

    public function update(UpdateCategoryRequest $request, Category $category): RedirectResponse
    {
        $this->service->updateCategory($category, $request->validated());

        return back();
    }

    public function destroy(Category $category): RedirectResponse
    {
        $this->service->deleteCategory($category);

        return back();
    }

    public function restore(Request $request, string $id): RedirectResponse
    {
        $this->service->restoreCategory($id, $request->user());

        return back()->with('success', 'Category restored successfully!');
    }

    public function forceDelete(Request $request, string $id): RedirectResponse
    {
        $this->service->forceDeleteCategory($id, $request->user());

        return back()->with('success', 'Category permanently deleted.');
    }
}
