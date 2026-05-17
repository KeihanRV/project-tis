<?php

namespace Database\Factories;

use App\Models\Report;
use App\Models\Trash;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Report>
 */
class ReportFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'title' => $this->faker->sentence(),
            'description' => $this->faker->paragraph(),
            'status' => $this->faker->randomElement(['pending', 'in_progress', 'completed', 'rejected']),
            'location' => $this->faker->address(),
            'image_path' => null,
        ];
    }

    /**
     * State to assign multiple random trashes to report
     */
    public function withTrash(): static
    {
        return $this->afterCreating(function (Report $report) {
            $trashCount = $this->faker->numberBetween(1, 3);
            $trashIds = Trash::inRandomOrder()->limit($trashCount)->pluck('id');

            $report->trash()->attach($trashIds);
        });
    }
}
