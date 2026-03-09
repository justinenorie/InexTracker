<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Transaction>
 */
class TransactionFactory extends Factory
{
    protected $model = Transaction::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $type = $this->faker->randomElement(['income', 'expense']);
        $amount = $this->faker->randomFloat(2, 10, 1000);

        return [
            'user_id' => User::factory(),
            'category_id' => Category::factory(),
            'type' => $type,
            'amount' => $type === 'expense' ? -$amount : $amount,
            'description' => $this->faker->sentence(),
            'transacted_at' => $this->faker->date(),
        ];
    }
}
