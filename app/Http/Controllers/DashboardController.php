<?php

namespace App\Http\Controllers;

use App\Services\CategoryService;
use App\Services\DashboardMetricsService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    protected $service;

    protected $categoryService;

    public function __construct(DashboardMetricsService $service, CategoryService $categoryService)
    {
        $this->service = $service;
        $this->categoryService = $categoryService;
    }

    public function index(Request $request): Response
    {
        $user = $request->user();

        $filters = $request->only(['from', 'to']);

        return Inertia::render('Dashboard', [
            'filters' => $filters,
            'totals' => $this->service->totals($user, $filters),
            'totalsByCategory' => $this->service->totalsByCategory($user, $filters),
            'history' => $this->service->getHistory($user, $filters),
            'categories' => $this->categoryService->getCategoriesForUser($user),
        ]);
    }
}
