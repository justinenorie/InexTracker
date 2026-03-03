<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::query()->first() ?? User::factory()->create([
            'name' => 'Seeder User',
            'email' => 'seeder+'.Str::random(8).'@example.com',
        ]);

        $now = now();

        DB::table('categories')->upsert([
            [
                'user_id' => $user->id,
                'name' => 'Salary',
                'type' => 'income',
                'color' => '#16a34a',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'user_id' => $user->id,
                'name' => 'Freelance',
                'type' => 'income',
                'color' => '#22c55e',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'user_id' => $user->id,
                'name' => 'Food',
                'type' => 'expense',
                'color' => '#ef4444',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'user_id' => $user->id,
                'name' => 'Transport',
                'type' => 'expense',
                'color' => '#f97316',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'user_id' => $user->id,
                'name' => 'Utilities',
                'type' => 'expense',
                'color' => '#eab308',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ], ['user_id', 'name', 'type'], ['color', 'updated_at']);
    }
}
