<?php

namespace Database\Factories;

use App\Models\Donation;
use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Donation>
 */
class DonationFactory extends Factory
{
    protected $model = Donation::class;

    public function definition()
    {
        return [
            'project_id' => Project::inRandomOrder()->first()->id ?? Project::factory(),
            'name' => $this->faker->name,
            'amount' => $this->faker->numberBetween(5000, 50000),
            'payment_method' => 'FIB',
        ];
    }
}
