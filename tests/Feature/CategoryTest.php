<?php

use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Inertia\Testing\AssertableInertia as Assert;

test('users can view their categories', function () {
    $user = User::factory()->create();
    // Unique names to avoid constraint violation
    Category::factory()->create(['user_id' => $user->id, 'name' => 'Cat 1']);
    Category::factory()->create(['user_id' => $user->id, 'name' => 'Cat 2']);
    Category::factory()->create(['user_id' => $user->id, 'name' => 'Cat 3']);

    $response = $this->actingAs($user)->get(route('categories.index'));

    $response->assertOk();
    $response->assertInertia(fn (Assert $page) => $page
        ->component('categories/Index')
        ->has('categories', 3)
    );
});

test('users can create a category', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->withoutMiddleware(ValidateCsrfToken::class)->post(route('categories.store'), [
        'name' => 'New Unique Category',
        'type' => 'expense',
        'color' => '#ff0000',
    ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('categories', [
        'user_id' => $user->id,
        'name' => 'New Unique Category',
        'type' => 'expense',
    ]);
});

test('users can update their category', function () {
    $user = User::factory()->create();
    $category = Category::factory()->create(['user_id' => $user->id]);

    $response = $this->actingAs($user)->withoutMiddleware(ValidateCsrfToken::class)->put(route('categories.update', $category), [
        'name' => 'Updated Unique Name',
        'type' => 'income',
        'color' => '#00ff00',
    ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('categories', [
        'id' => $category->id,
        'name' => 'Updated Unique Name',
        'type' => 'income',
    ]);
});

test('users cannot update others categories', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $category = Category::factory()->create(['user_id' => $otherUser->id]);

    $response = $this->actingAs($user)->withoutMiddleware(ValidateCsrfToken::class)->put(route('categories.update', $category), [
        'name' => 'Hack Attempt',
    ]);

    $response->assertForbidden();
});

test('users can soft delete their category and its transactions are also soft deleted', function () {
    $user = User::factory()->create();
    $category = Category::factory()->create(['user_id' => $user->id]);
    $transaction = \App\Models\Transaction::factory()->create([
        'user_id' => $user->id,
        'category_id' => $category->id,
    ]);

    $response = $this->actingAs($user)->withoutMiddleware(ValidateCsrfToken::class)->delete(route('categories.destroy', $category));

    $response->assertRedirect();
    $this->assertSoftDeleted($category);
    $this->assertSoftDeleted($transaction);
});

test('users can restore a deleted category and its transactions are also restored', function () {
    $user = User::factory()->create();
    $category = Category::factory()->create(['user_id' => $user->id]);
    $transaction = \App\Models\Transaction::factory()->create([
        'user_id' => $user->id,
        'category_id' => $category->id,
    ]);

    $category->transactions()->delete();
    $category->delete();

    $response = $this->actingAs($user)->withoutMiddleware(ValidateCsrfToken::class)->post(route('categories.restore', $category->id));

    $response->assertRedirect();
    $this->assertDatabaseHas('categories', [
        'id' => $category->id,
        'deleted_at' => null,
    ]);
    $this->assertDatabaseHas('transactions', [
        'id' => $transaction->id,
        'deleted_at' => null,
    ]);
});

test('users can force delete a category and its transactions are also force deleted', function () {
    $user = User::factory()->create();
    $category = Category::factory()->create(['user_id' => $user->id]);
    $transaction = \App\Models\Transaction::factory()->create([
        'user_id' => $user->id,
        'category_id' => $category->id,
    ]);

    $category->transactions()->delete();
    $category->delete();

    $response = $this->actingAs($user)->withoutMiddleware(ValidateCsrfToken::class)->delete(route('categories.force-delete', $category->id));

    $response->assertRedirect();
    $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    $this->assertDatabaseMissing('transactions', ['id' => $transaction->id]);
});
