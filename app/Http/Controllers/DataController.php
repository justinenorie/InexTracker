<?php

namespace App\Http\Controllers;

use App\Services\DashboardMetricsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DataController extends Controller
{
    /** @var DashboardMetricsService */
    protected $metricsService;

    /**
     * @param  DashboardMetricsService  $metricsService the metrics service instance
     * @return void
     */
    public function __construct(DashboardMetricsService $metricsService)
    {
        $this->metricsService = $metricsService;
    }

    /**
     * Export transactions to a CSV file.
     *
     * @param  Request  $request the incoming request
     * @return StreamedResponse
     */
    public function exportTransactionsCsv(Request $request): StreamedResponse
    {
        $user = $request->user();
        $filters = $request->only(['type', 'category_id', 'from', 'to', 'search']);

        $fileName = 'transactions_'.now()->format('Y-m-d_His').'.csv';

        $headers = [
            'Content-type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=$fileName",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $columns = ['Date', 'Type', 'Category', 'Amount', 'Description'];

        $callback = function () use ($user, $filters, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns, ',', '"', '');

            // Using chunking for memory efficiency
            $query = $this->metricsService->queryForUser($user)->with('category');
            $this->metricsService->applyFilters($query, $filters);
            $this->metricsService->applyDefaultSort($query);

            $query->chunk(500, function ($transactions) use ($file) {
                foreach ($transactions as $t) {
                    fputcsv($file, [
                        $t->transacted_at,
                        ucfirst($t->type),
                        $t->category?->name ?? 'N/A',
                        $t->amount,
                        $t->description ?? '',
                    ], ',', '"', '');
                }
            });

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }
}
