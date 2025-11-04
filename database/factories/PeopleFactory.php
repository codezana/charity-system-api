<?php

namespace Database\Factories;
use App\Models\Cases;
use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;


/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\People>
 */
class PeopleFactory extends Factory
{
    protected $model = \App\Models\People::class;

    public function definition()
    {
        return [
            'project_id' => Project::inRandomOrder()->first()->id ?? Project::factory(),
            'name' => $this->faker->name,
            'phone' => $this->faker->phoneNumber,
            'address' => $this->faker->address,
            'case_id' => Cases::inRandomOrder()->first()->id ?? Cases::factory(),
            'aid' => $this->faker->numberBetween(5000, 50000),
            'date_received' => $this->faker->date,
        ];
    }
}
