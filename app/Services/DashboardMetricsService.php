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
     * @param  User  $user the user instance
     * @return Builder<Transaction>
     */
    public function queryForUser(User $user): Builder
    {
        return Transaction::query()->where('transactions.user_id', $user->id);
    }

    /**
     * Apply common filters.
     *
     * @param  Builder<Transaction>  $query the query builder instance
     * @param  array  $filters the filters to apply
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
     * @param  Builder<Transaction>  $query the query builder instance
     * @return Builder<Transaction>
     */
    public function applyDefaultSort(Builder $query): Builder
    {
        return $query
            ->orderByDesc('transactions.transacted_at')
            ->orderByDesc('transactions.id');
    }

    /**
     * Dashboard totals (income, expense) scoped to a user.
     *
     * @param  User  $user the user instance
     * @param  array  $filters the dashboard filters
     * @return array
     */
    public function totals(User $user, array $filters = []): array
    {
        $base = $this->applyFilters(
            $this->queryForUser($user),
            $filters,
        );

        $totalIncome = $this->sumAmount((clone $base)->where('type', 'income'));
        $totalExpenseRaw = $this->sumAmount((clone $base)->where('type', 'expense'));

        // Show expenses as positive on the dashboard
        $totalExpense = (string) abs((float) $totalExpenseRaw);

        return [
            'total_income' => $totalIncome,
            'total_expense' => $totalExpense,
        ];
    }

    /**
     * Breakdown totals by category.
     *
     * @param  User  $user the user instance
     * @param  array  $filters the dashboard filters
     * @return array
     */
    public function totalsByCategory(User $user, array $filters = []): array
    {
        $base = $this->applyFilters(
            $this->queryForUser($user),
            $filters,
        );

        return (clone $base)
            ->join('categories as c', 'c.id', '=', 'transactions.category_id')
            ->groupBy('transactions.category_id', 'c.name', 'c.color', 'transactions.type')
            ->orderByDesc(DB::raw('SUM(transactions.amount)'))
            ->get([
                'transactions.category_id as category_id',
                'c.name as category_name',
                'c.color as category_color',
                'transactions.type as type',
                DB::raw('COALESCE(SUM(transactions.amount), 0) as total'),
            ])
            ->map(function ($row) {
                return [
                    'category_id' => (string) $row->category_id,
                    'category_name' => (string) $row->category_name,
                    'category_color' => (string) $row->category_color,
                    'type' => (string) $row->type,
                    'total' => (string) $row->total,
                ];
            })
            ->all();
    }

    /**
     * Get historical totals (income vs expense) grouped by period (day or month).
     *
     * @param  User  $user the user instance
     * @param  array  $filters the dashboard filters
     * @return array
     */
    public function getHistory(User $user, array $filters = []): array
    {
        $query = $this->queryForUser($user);
        $this->applyFilters($query, $filters);

        $from = $this->parseDate($filters['from'] ?? null);
        $to = $this->parseDate($filters['to'] ?? null);

        if (! $from || ! $to) {
            $to = Carbon::today();
            $from = Carbon::today()->subMonths(5)->startOfMonth();

            $query->whereBetween('transactions.transacted_at', [$from->toDateString(), $to->toDateString()]);
        }

        $daysDiff = $from->diffInDays($to);
        $groupByDay = $daysDiff <= 60;

        $driver = DB::getDriverName();

        if ($driver === 'sqlite') {
            $dateFormat = $groupByDay ? '%Y-%m-%d' : '%Y-%m';
            $dateSelect = "strftime('{$dateFormat}', transacted_at)";
        } else {
            $dateFormat = $groupByDay ? '%Y-%m-%d' : '%Y-%m';
            $dateSelect = "DATE_FORMAT(transacted_at, '{$dateFormat}')";
        }

        $results = $query
            ->select([
                DB::raw("{$dateSelect} as period"),
                'type',
                DB::raw('SUM(amount) as total'),
            ])
            ->groupBy('period', 'type')
            ->orderBy('period')
            ->get();

        $data = [];

        $current = clone $from;
        while ($current <= $to) {
            $period = $current->format($groupByDay ? 'Y-m-d' : 'Y-m');
            $data[$period] = [
                'period' => $period,
                'label' => $current->format($groupByDay ? 'M d' : 'M Y'),
                'income' => 0.0,
                'expense' => 0.0,
            ];

            if ($groupByDay) {
                $current->addDay();
            } else {
                $current->addMonth();
            }
        }

        foreach ($results as $row) {
            $period = (string) $row->period;

            if (! isset($data[$period])) {
                continue;
            }

            if ($row->type === 'income') {
                $data[$period]['income'] = (float) $row->total;
            } else {
                $data[$period]['expense'] = abs((float) $row->total);
            }
        }

        return array_values($data);
    }

    /**
     * Sum the amounts for the given query.
     *
     * @param  Builder  $query the query builder instance
     * @return string
     */
    private function sumAmount(Builder $query): string
    {
        $value = (clone $query)
            ->selectRaw('COALESCE(SUM(transactions.amount), 0) as total')
            ->value('total');

        return (string) $value;
    }

    /**
     * Parse the given value as a Carbon date.
     *
     * @param  mixed  $value the value to parse
     * @return Carbon|null
     */
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
