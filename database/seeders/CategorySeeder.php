<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Seeder;
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
            'email' => 'seeder+' . Str::random(8) . '@example.com',
        ]);

        $defaults = [
            ['Salary', 'income', '#16a34a'],
            ['Freelance', 'income', '#22c55e'],
            ['Food', 'expense', '#ef4444'],
            ['Transport', 'expense', '#f97316'],
            ['Utilities', 'expense', '#eab308'],
        ];

        foreach ($defaults as [$name, $type, $color]) {
            Category::withTrashed()->updateOrCreate(
                [
                    'user_id' => $user->id,
                    'name' => $name,
                    'type' => $type,
                ],
                [
                    'color' => $color,
                ],
            );
        }

        Category::onlyTrashed()
            ->where('user_id', $user->id)
            ->whereIn('name', array_column($defaults, 0))
            ->restore();
    }
}
