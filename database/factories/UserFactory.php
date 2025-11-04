<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
 
    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->userName(),
            'password' => Hash::make('admin@admin'),
            'role' => $this->faker->randomElement(['admin','staff'])
        ];
    }

}
