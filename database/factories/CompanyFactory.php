<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Company>
 */
class CompanyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->company,
            'description' => $this->faker->paragraph,
            'rating' => $this->faker->numberBetween(1, 5),
            'website' => $this->faker->url,
            'employes' => $this->faker->numberBetween(1, 1000),
        ];
    }
}
