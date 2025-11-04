<?php

namespace Database\Factories;

use App\Models\Donation;
use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Expense>
 */
class ExpenseFactory extends Factory
{
    protected $model = \App\Models\Expense::class;

    public function definition()
    {
        return [
            'project_id' => \App\Models\Project::inRandomOrder()->first()->id ?? \App\Models\Project::factory(),
            'made' => \App\Models\User::inRandomOrder()->first()->id ?? null,
            'name' => $this->faker->name,
            'category' => \App\Models\Category::inRandomOrder()->first()->id ?? null,
            'description' => $this->faker->optional()->text,
            'price' => $this->faker->numberBetween(5000, 50000),
            'quantity' => $this->faker->numberBetween(1, 10),
            'total' => $this->faker->numberBetween(5000, 50000),
            'payment_method' => 'FIB',
            'paid' => $this->faker->numberBetween(0, 50000),
            'invoice' => $this->faker->optional()->text,
            'status' => $this->faker->randomElement(['paid', 'unpaid']),
        ];
    }
}
