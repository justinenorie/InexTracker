<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class TransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::query()->first();

        if (! $user) {
            return;
        }

        $alreadySeeded = DB::table('transactions')
            ->where('user_id', $user->id)
            ->exists();

        if ($alreadySeeded) {
            return;
        }

        $incomeCategoryIds = DB::table('categories')
            ->where('user_id', $user->id)
            ->whereIn('type', ['income', 'both'])
            ->pluck('id')
            ->all();

        $expenseCategoryIds = DB::table('categories')
            ->where('user_id', $user->id)
            ->whereIn('type', ['expense', 'both'])
            ->pluck('id')
            ->all();

        if ($incomeCategoryIds === [] || $expenseCategoryIds === []) {
            return;
        }

        $now = now();
        $rows = [];

        for ($i = 0; $i < 10; $i++) {
            $rows[] = [
                'user_id' => $user->id,
                'category_id' => $incomeCategoryIds[array_rand($incomeCategoryIds)],
                'type' => 'income',
                'amount' => fake()->randomFloat(2, 200, 5000),
                'description' => fake()->sentence(4),
                'transacted_at' => Carbon::today()->subDays(fake()->numberBetween(0, 60)),
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        for ($i = 0; $i < 15; $i++) {
            $rows[] = [
                'user_id' => $user->id,
                'category_id' => $expenseCategoryIds[array_rand($expenseCategoryIds)],
                'type' => 'expense',
                'amount' => fake()->randomFloat(2, 5, 800),
                'description' => fake()->sentence(5),
                'transacted_at' => Carbon::today()->subDays(fake()->numberBetween(0, 60)),
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        DB::table('transactions')->insert($rows);
    }
}
