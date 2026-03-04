<?php

namespace App\Http\Controllers;

use App\Services\DashboardMetricsService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    protected $service;

    public function __construct(DashboardMetricsService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request): Response
    {
        $user = $request->user();

        $filters = $request->only(['from', 'to']);

        return Inertia::render('Dashboard', [
            'filters' => $filters,
            'totals' => $this->service->totals($user, $filters),
            'totalsByCategory' => $this->service->totalsByCategory($user, $filters),
        ]);
    }
}
