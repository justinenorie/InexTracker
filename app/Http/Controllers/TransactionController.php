<?php

namespace App\Http\Controllers;

use App\Http\Requests\Transaction\StoreTransactionRequest;
use App\Http\Requests\Transaction\UpdateTransactionRequest;
use App\Models\Transaction;
use App\Services\CategoryService;
use App\Services\TransactionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class TransactionController extends Controller
{
    /** @var TransactionService */
    protected $service;

    /** @var CategoryService */
    protected $categoryService;

    /**
     * @param  TransactionService  $service the transaction service instance
     * @param  CategoryService  $categoryService the category service instance
     * @return void
     */
    public function __construct(TransactionService $service, CategoryService $categoryService)
    {
        $this->service = $service;
        $this->categoryService = $categoryService;
    }

    /**
     * Display a listing of transactions.
     *
     * @param  Request  $request the incoming request
     * @return Response
     */
    public function index(Request $request): Response
    {
        $user = $request->user();

        $filters = $request->only([
            'type',
            'category_id',
            'from',
            'to',
            'search',
        ]);

        return Inertia::render('transactions/Index', [
            'filters' => $filters,
            'transactions' => $this->service->listTransactions($user, $filters),
            'categories' => $this->service->getCategoriesForSelection($user),
        ]);
    }

    /**
     * Display a listing of trashed transactions.
     *
     * @param  Request  $request the incoming request
     * @return Response
     */
    public function trash(Request $request): Response
    {
        $user = $request->user();

        return Inertia::render('Trash/Index', [
            'transactions' => $this->service->listTrashedTransactions($user),
            'categories' => $this->categoryService->getTrashedCategoriesForUser($user),
        ]);
    }

    /**
     * Store a new transaction.
     *
     * @param  StoreTransactionRequest  $request the store request
     * @return RedirectResponse
     */
    public function store(StoreTransactionRequest $request): RedirectResponse
    {
        $this->service->createTransaction($request->validated());

        return back()->with('success', 'Transaction recorded successfully!');
    }

    /**
     * Update an existing transaction.
     *
     * @param  UpdateTransactionRequest  $request the update request
     * @param  Transaction  $transaction the transaction model instance
     * @return RedirectResponse
     */
    public function update(UpdateTransactionRequest $request, Transaction $transaction): RedirectResponse
    {
        $this->service->updateTransaction($transaction, $request->validated());

        return back();
    }

    /**
     * Delete an existing transaction.
     *
     * @param  Transaction  $transaction the transaction model instance
     * @return RedirectResponse
     */
    public function destroy(Transaction $transaction): RedirectResponse
    {
        $this->service->deleteTransaction($transaction);

        return back();
    }

    /**
     * Restore a trashed transaction.
     *
     * @param  Request  $request the incoming request
     * @param  string  $id the transaction unique id
     * @return RedirectResponse
     */
    public function restore(Request $request, string $id): RedirectResponse
    {
        $this->service->restoreTransaction($id, $request->user());

        return back()->with('success', 'Transaction restored successfully!');
    }

    /**
     * Permanently delete a trashed transaction.
     *
     * @param  Request  $request the incoming request
     * @param  string  $id the transaction unique id
     * @return RedirectResponse
     */
    public function forceDelete(Request $request, string $id): RedirectResponse
    {
        $this->service->forceDeleteTransaction($id, $request->user());

        return back()->with('success', 'Transaction permanently deleted.');
    }
}
