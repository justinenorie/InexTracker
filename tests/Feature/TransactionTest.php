<?php

use App\Models\Category;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Inertia\Testing\AssertableInertia as Assert;

test('users can view their transactions', function () {
    $user = User::factory()->create();
    $category = Category::factory()->create(['user_id' => $user->id]);
    Transaction::factory()->count(5)->create([
        'user_id' => $user->id,
        'category_id' => $category->id,
    ]);

    $response = $this->actingAs($user)->get(route('transactions.index'));

    $response->assertOk();
    $response->assertInertia(fn (Assert $page) => $page
        ->component('transactions/Index')
        ->has('transactions.data', 5)
    );
});

test('users can create a transaction and user balance is updated', function () {
    $user = User::factory()->create(['balance' => 1000]);
    $category = Category::factory()->create(['user_id' => $user->id, 'type' => 'expense']);

    $response = $this->actingAs($user)->withoutMiddleware(ValidateCsrfToken::class)->post(route('transactions.store'), [
        'type' => 'expense',
        'category_id' => $category->id,
        'amount' => 200,
        'transacted_at' => now()->toDateString(),
        'description' => 'Test Expense',
    ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('transactions', [
        'user_id' => $user->id,
        'amount' => -200,
        'type' => 'expense',
    ]);

    expect($user->fresh()->balance)->toEqual(800);
});

test('users can update a transaction and balance is recalculated', function () {
    $user = User::factory()->create(['balance' => 800]);
    $category = Category::factory()->create(['user_id' => $user->id]);
    $transaction = Transaction::factory()->create([
        'user_id' => $user->id,
        'category_id' => $category->id,
        'type' => 'expense',
        'amount' => -200,
    ]);

    $user->update(['balance' => 800]);

    $response = $this->actingAs($user)->withoutMiddleware(ValidateCsrfToken::class)->put(route('transactions.update', $transaction), [
        'type' => 'expense',
        'category_id' => $category->id,
        'amount' => 300,
        'transacted_at' => now()->toDateString(),
    ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('transactions', [
        'id' => $transaction->id,
        'amount' => -300,
    ]);

    expect($user->fresh()->balance)->toEqual(700);
});

test('users cannot update others transactions', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $category = Category::factory()->create(['user_id' => $otherUser->id]);
    $transaction = Transaction::factory()->create([
        'user_id' => $otherUser->id,
        'category_id' => $category->id,
    ]);

    $response = $this->actingAs($user)->withoutMiddleware(ValidateCsrfToken::class)->put(route('transactions.update', $transaction), [
        'amount' => 500,
    ]);

    $response->assertForbidden();
});

test('users can soft delete a transaction and balance is updated', function () {
    $user = User::factory()->create(['balance' => 700]);
    $category = Category::factory()->create(['user_id' => $user->id]);
    $transaction = Transaction::factory()->create([
        'user_id' => $user->id,
        'category_id' => $category->id,
        'type' => 'expense',
        'amount' => -300,
    ]);
    $user->update(['balance' => 700]);

    $response = $this->actingAs($user)->withoutMiddleware(ValidateCsrfToken::class)->delete(route('transactions.destroy', $transaction));

    $response->assertRedirect();
    $this->assertSoftDeleted($transaction);

    expect($user->fresh()->balance)->toEqual(1000);
});

test('users can view trash', function () {
    $user = User::factory()->create();
    $category = Category::factory()->create(['user_id' => $user->id]);
    $transaction = Transaction::factory()->create([
        'user_id' => $user->id,
        'category_id' => $category->id,
    ]);
    $transaction->delete();

    $response = $this->actingAs($user)->get(route('transactions.trash'));

    $response->assertOk();
    $response->assertInertia(fn (Assert $page) => $page
        ->component('Trash/Index')
        ->has('transactions', 1)
    );
});

test('users can restore a transaction', function () {
    $user = User::factory()->create(['balance' => 1000]);
    $category = Category::factory()->create(['user_id' => $user->id]);
    $transaction = Transaction::factory()->create([
        'user_id' => $user->id,
        'category_id' => $category->id,
        'type' => 'expense',
        'amount' => -300,
    ]);
    $transaction->delete();
    $user->update(['balance' => 1000]);

    $response = $this->actingAs($user)->withoutMiddleware(ValidateCsrfToken::class)->post(route('transactions.restore', $transaction->id));

    $response->assertRedirect();
    $this->assertDatabaseHas('transactions', [
        'id' => $transaction->id,
        'deleted_at' => null,
    ]);

    expect($user->fresh()->balance)->toEqual(700);
});
