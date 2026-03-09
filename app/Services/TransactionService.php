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

    protected $softDeletes;

    public function __construct(DashboardMetricsService $queryService, SoftDeleteService $softDeletes)
    {
        $this->queryService = $queryService;
        $this->softDeletes = $softDeletes;
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

        $user = Auth::user();
        $data['user_id'] = $user->id;

        $transaction = Transaction::create($data);

        $user->increment('balance', (float) $transaction->amount);

        return $transaction;
    }

    /**
     * Update an existing transaction.
     */
    public function updateTransaction(Transaction $transaction, array $data): bool
    {
        $oldAmount = $transaction->amount;

        if (isset($data['type']) && isset($data['amount'])) {
            if ($data['type'] === 'expense' && $data['amount'] > 0) {
                $data['amount'] = $data['amount'] * -1;
            } elseif ($data['type'] === 'income' && $data['amount'] < 0) {
                $data['amount'] = abs($data['amount']);
            }
        }

        $updated = $transaction->update($data);

        if ($updated) {
            $diff = $transaction->amount - $oldAmount;
            if ($diff != 0) {
                $transaction->user->increment('balance', $diff);
            }
        }

        return $updated;
    }

    /**
     * Delete a transaction.
     */
    public function deleteTransaction(Transaction $transaction): ?bool
    {
        $amount = $transaction->amount;
        $user = $transaction->user;

        $deleted = $transaction->delete();

        if ($deleted) {
            $user->decrement('balance', (float) $amount);
        }

        return $deleted;
    }

    /**
     * Get trashed transactions for a user.
     */
    public function listTrashedTransactions(User $user): Collection
    {
        return $this->softDeletes->listTrashedForUser(
            Transaction::class,
            $user,
            with: ['category'],
        );
    }

    /**
     * Restore a trashed transaction.
     */
    public function restoreTransaction(string $id, User $user): bool
    {
        $restored = $this->softDeletes->restoreForUser(Transaction::class, $id, $user);

        if ($restored) {
            $transaction = Transaction::find($id);
            if ($transaction) {
                $user->increment('balance', $transaction->amount);
            }
        }

        return $restored;
    }

    /**
     * Permanently delete a transaction.
     */
    public function forceDeleteTransaction(string $id, User $user): bool
    {
        return $this->softDeletes->forceDeleteForUser(Transaction::class, $id, $user);
    }

    /**
     * Recalculate the user's balance based on initial_balance and all transactions.
     */
    public function recalculateBalance(User $user): void
    {
        $transactionsSum = Transaction::query()
            ->where('user_id', $user->id)
            ->sum('amount');

        $user->update([
            'balance' => $user->initial_balance + $transactionsSum,
        ]);
    }
}
