<?php

namespace Database\Factories;

use App\Models\Cases;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class CaseFactory extends Factory
{
    protected $model = Cases::class;

    public function definition()
    {
        return [
            'name' => $this->faker->name,
        ];
    }
}
