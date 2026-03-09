<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TransactionService
{
    /** @var DashboardMetricsService */
    protected $queryService;

    /** @var SoftDeleteService */
    protected $softDeletes;

    /**
     * @param  DashboardMetricsService  $queryService the dashboard metrics service
     * @param  SoftDeleteService  $softDeletes the soft delete service
     * @return void
     */
    public function __construct(DashboardMetricsService $queryService, SoftDeleteService $softDeletes)
    {
        $this->queryService = $queryService;
        $this->softDeletes = $softDeletes;
    }

    /**
     * Get a paginated list of transactions for a user.
     *
     * @param  User  $user the user instance
     * @param  array  $filters the search filters
     * @return LengthAwarePaginator
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
     *
     * @param  User  $user the user instance
     * @return Collection
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
     *
     * @param  array  $data the transaction data
     * @return Transaction
     */
    public function createTransaction(array $data): Transaction
    {
        return DB::transaction(function () use ($data) {
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
        });
    }

    /**
     * Update an existing transaction.
     *
     * @param  Transaction  $transaction the transaction instance
     * @param  array  $data the update data
     * @return bool
     */
    public function updateTransaction(Transaction $transaction, array $data): bool
    {
        return DB::transaction(function () use ($transaction, $data) {
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
        });
    }

    /**
     * Delete a transaction.
     *
     * @param  Transaction  $transaction the transaction instance
     * @return bool|null
     */
    public function deleteTransaction(Transaction $transaction): ?bool
    {
        return DB::transaction(function () use ($transaction) {
            $amount = $transaction->amount;
            $user = $transaction->user;

            $deleted = $transaction->delete();

            if ($deleted) {
                $user->decrement('balance', (float) $amount);
            }

            return $deleted;
        });
    }

    /**
     * Get trashed transactions for a user.
     *
     * @param  User  $user the user instance
     * @return Collection
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
     *
     * @param  string  $id the unique id
     * @param  User  $user the user instance
     * @return bool
     */
    public function restoreTransaction(string $id, User $user): bool
    {
        return DB::transaction(function () use ($id, $user) {
            $restored = $this->softDeletes->restoreForUser(Transaction::class, $id, $user);

            if ($restored) {
                $transaction = Transaction::find($id);
                if ($transaction) {
                    $user->increment('balance', $transaction->amount);
                }
            }

            return $restored;
        });
    }

    /**
     * Permanently delete a transaction.
     *
     * @param  string  $id the unique id
     * @param  User  $user the user instance
     * @return bool
     */
    public function forceDeleteTransaction(string $id, User $user): bool
    {
        return $this->softDeletes->forceDeleteForUser(Transaction::class, $id, $user);
    }

    /**
     * Recalculate the user's balance based on initial_balance and all transactions.
     *
     * @param  User  $user the user instance
     * @return void
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
