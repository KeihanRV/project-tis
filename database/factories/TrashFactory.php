<?php

namespace Database\Factories;

use App\Models\Trash;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Trash>
 */
class TrashFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->word(),
            'category' => $this->faker->randomElement(['Anorganik', 'Organik', 'Berbahaya']),
            'weight' => $this->faker->randomFloat(2, 0.1, 100), // weight in kg
        ];
    }
}
