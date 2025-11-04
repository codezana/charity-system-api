<?php

namespace Database\Factories;

use App\Models\Debt;
use App\Models\Expense;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Debt>
 */
class DebtFactory extends Factory
{
    protected $model = Debt::class;

    public function definition(): array
    {
        return [
            'expense_id' => Expense::inRandomOrder()->first()->id ?? Expense::factory(),
            'paid' => $this->faker->numberBetween(0, 10000) / 100,
            'due_date' => $this->faker->dateTimeBetween('-1 year', '1 year'),
        ];
    }
}

