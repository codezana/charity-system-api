<?php

namespace Database\Factories;

use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Project>
 */
class ProjectFactory extends Factory
{
    protected $model = Project::class;

    public function definition()
    {
        return [
            'made' => User::inRandomOrder()->first()->id ?? User::factory(),
            'title' => $this->faker->sentence(3),
            'description' => $this->faker->paragraph,
            'location' => $this->faker->city,
            'start_date' => $this->faker->date,
            'goal_amount' => $this->faker->numberBetween(100000, 500000),
            'total_donations' => 0,
            'total_expenses' => 0,
            'balance' => 0,
            'end_date' => $this->faker->date,
            'status' => 'active',
        ];
    }
}
