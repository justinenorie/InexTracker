<?php

namespace App\Services;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardMetricsService
{
    /**
     * Start a base transaction query scoped to a user.
     *
     * @return Builder<Transaction>
     */
    public function queryForUser(User $user): Builder
    {
        return Transaction::query()->where('transactions.user_id', $user->id);
    }

    /**
     * Apply common filters.
     *
     * @param  Builder<Transaction>  $query
     * @param  array<string, mixed>  $filters
     * @return Builder<Transaction>
     */
    public function applyFilters(Builder $query, array $filters): Builder
    {
        if (isset($filters['type']) && in_array($filters['type'], ['income', 'expense'], true)) {
            $query->where('transactions.type', $filters['type']);
        }

        if (isset($filters['category_id']) && is_numeric($filters['category_id'])) {
            $query->where('transactions.category_id', (string) $filters['category_id']);
        } elseif (isset($filters['category_id']) && is_string($filters['category_id'])) {
            $categoryId = trim($filters['category_id']);

            if ($categoryId !== '') {
                $query->where('transactions.category_id', $categoryId);
            }
        }

        $from = $this->parseDate($filters['from'] ?? null);
        $to = $this->parseDate($filters['to'] ?? null);

        if ($from && $to) {
            $query->whereBetween('transactions.transacted_at', [$from->toDateString(), $to->toDateString()]);
        } elseif ($from) {
            $query->whereDate('transactions.transacted_at', '>=', $from->toDateString());
        } elseif ($to) {
            $query->whereDate('transactions.transacted_at', '<=', $to->toDateString());
        }

        if (isset($filters['search']) && is_string($filters['search'])) {
            $search = trim($filters['search']);

            if ($search !== '') {
                $query->where(function (Builder $q) use ($search) {
                    $q->where('transactions.description', 'like', "%{$search}%")
                        ->orWhereHas('category', function (Builder $c) use ($search) {
                            $c->where('name', 'like', "%{$search}%");
                        });
                });
            }
        }

        return $query;
    }

    /**
     * Apply the default ordering for lists.
     *
     * @param  Builder<Transaction>  $query
     * @return Builder<Transaction>
     */
    public function applyDefaultSort(Builder $query): Builder
    {
        return $query
            ->orderByDesc('transactions.transacted_at')
            ->orderByDesc('transactions.id');
    }

    /**
     * Dashboard totals (income, expense, revenue) scoped to a user.
     *
     * @param  array<string, mixed>  $filters
     * @return array{total_income: string, total_expense: string, revenue: string}
     */
    public function totals(User $user, array $filters = []): array
    {
        $base = $this->applyFilters(
            $this->queryForUser($user),
            $filters,
        );

        $totalIncome = $this->sumAmount((clone $base)->where('type', 'income'));
        $totalExpense = $this->sumAmount((clone $base)->where('type', 'expense'));

        return [
            'total_income' => $totalIncome,
            'total_expense' => $totalExpense,
            'revenue' => $this->decimalSub($totalIncome, $totalExpense),
        ];
    }

    /**
     * Breakdown totals by category.
     *
     * @param  array<string, mixed>  $filters
     * @return array<int, array{category_id: string, category_name: string, type: string, total: string}>
     */
    public function totalsByCategory(User $user, array $filters = []): array
    {
        $base = $this->applyFilters(
            $this->queryForUser($user),
            $filters,
        );

        return (clone $base)
            ->join('categories as c', 'c.id', '=', 'transactions.category_id')
            ->groupBy('transactions.category_id', 'c.name', 'transactions.type')
            ->orderByDesc(DB::raw('SUM(transactions.amount)'))
            ->get([
                'transactions.category_id as category_id',
                'c.name as category_name',
                'transactions.type as type',
                DB::raw('COALESCE(SUM(transactions.amount), 0) as total'),
            ])
            ->map(function ($row) {
                return [
                    'category_id' => (string) $row->category_id,
                    'category_name' => (string) $row->category_name,
                    'type' => (string) $row->type,
                    'total' => (string) $row->total,
                ];
            })
            ->all();
    }

    /**
     * @param  Builder<\App\Models\Transaction>  $query
     */
    private function sumAmount(Builder $query): string
    {
        $value = (clone $query)
            ->selectRaw('COALESCE(SUM(transactions.amount), 0) as total')
            ->value('total');

        return (string) $value;
    }

    private function decimalSub(string $a, string $b): string
    {
        if (function_exists('bcsub')) {
            return bcsub($a, $b, 2);
        }

        return number_format(((float) $a) - ((float) $b), 2, '.', '');
    }

    private function parseDate(mixed $value): ?Carbon
    {
        if (! is_string($value) || trim($value) === '') {
            return null;
        }

        try {
            return Carbon::parse($value);
        } catch (\Throwable) {
            return null;
        }
    }
}
