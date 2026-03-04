<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

class TransactionService
{
    protected $queryService;

    public function __construct(DashboardMetricsService $queryService)
    {
        $this->queryService = $queryService;
    }

    /**
     * Get a paginated list of transactions for a user.
     */
    public function listTransactions(User $user, array $filters): LengthAwarePaginator
    {
        $query = $this->queryService->applyDefaultSort(
            $this->queryService->applyFilters(
                $this->queryService->queryForUser($user)->with('category'),
                $filters,
            )
        );

        return $query->paginate(10)->withQueryString();
    }

    /**
     * Get categories for the transaction selection.
     */
    public function getCategoriesForSelection(User $user): Collection
    {
        return Category::query()
            ->where('user_id', $user->id)
            ->orderBy('type')
            ->orderBy('name')
            ->get(['id', 'name', 'type', 'color']);
    }

    /**
     * Create a new transaction with business logic applied.
     */
    public function createTransaction(array $data): Transaction
    {
        if ($data['type'] === 'expense' && $data['amount'] > 0) {
            $data['amount'] = $data['amount'] * -1;
        } elseif ($data['type'] === 'income' && $data['amount'] < 0) {
            $data['amount'] = abs($data['amount']);
        }

        $data['user_id'] = Auth::id();

        return Transaction::create($data);
    }

    /**
     * Update an existing transaction.
     */
    public function updateTransaction(Transaction $transaction, array $data): bool
    {
        if (isset($data['type']) && isset($data['amount'])) {
            if ($data['type'] === 'expense' && $data['amount'] > 0) {
                $data['amount'] = $data['amount'] * -1;
            } elseif ($data['type'] === 'income' && $data['amount'] < 0) {
                $data['amount'] = abs($data['amount']);
            }
        }

        return $transaction->update($data);
    }

    /**
     * Delete a transaction.
     */
    public function deleteTransaction(Transaction $transaction): ?bool
    {
        return $transaction->delete();
    }
}
