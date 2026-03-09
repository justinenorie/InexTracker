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
    protected $service;

    protected $categoryService;

    public function __construct(TransactionService $service, CategoryService $categoryService)
    {
        $this->service = $service;
        $this->categoryService = $categoryService;
    }

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

    public function trash(Request $request): Response
    {
        $user = $request->user();

        return Inertia::render('Trash/Index', [
            'transactions' => $this->service->listTrashedTransactions($user),
            'categories' => $this->categoryService->getTrashedCategoriesForUser($user),
        ]);
    }

    public function store(StoreTransactionRequest $request): RedirectResponse
    {
        $this->service->createTransaction($request->validated());

        return back()->with('success', 'Transaction recorded successfully!');
    }

    public function update(UpdateTransactionRequest $request, Transaction $transaction): RedirectResponse
    {
        $this->service->updateTransaction($transaction, $request->validated());

        return back();
    }

    public function destroy(Transaction $transaction): RedirectResponse
    {
        $this->service->deleteTransaction($transaction);

        return back();
    }

    public function restore(Request $request, string $id): RedirectResponse
    {
        $this->service->restoreTransaction($id, $request->user());

        return back()->with('success', 'Transaction restored successfully!');
    }

    public function forceDelete(Request $request, string $id): RedirectResponse
    {
        $this->service->forceDeleteTransaction($id, $request->user());

        return back()->with('success', 'Transaction permanently deleted.');
    }
}
