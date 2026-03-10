<?php

namespace App\Http\Controllers;

use App\Services\CategoryService;
use App\Services\DashboardMetricsService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    /** @var DashboardMetricsService */
    protected $service;

    /** @var CategoryService */
    protected $categoryService;

    /**
     * @param  DashboardMetricsService  $service  the dashboard metrics service instance
     * @param  CategoryService  $categoryService  the category service instance
     * @return void
     */
    public function __construct(DashboardMetricsService $service, CategoryService $categoryService)
    {
        $this->service = $service;
        $this->categoryService = $categoryService;
    }

    /**
     * Display the dashboard.
     *
     * @param  Request  $request  the incoming request
     */
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
