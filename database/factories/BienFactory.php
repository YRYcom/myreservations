<?php

namespace Database\Factories;

use App\Models\Bien;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Bien>
 */
class BienFactory extends Factory
{
    protected $model = Bien::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->words(3, true),
            'capacity' => fake()->numberBetween(2, 12),
            'description' => fake()->paragraph(),
            'photo' => null,
        ];
    }

    /**
     * Indicate that the bien has a small capacity.
     */
    public function small(): static
    {
        return $this->state(fn (array $attributes) => [
            'capacity' => fake()->numberBetween(2, 4),
        ]);
    }

    /**
     * Indicate that the bien has a large capacity.
     */
    public function large(): static
    {
        return $this->state(fn (array $attributes) => [
            'capacity' => fake()->numberBetween(10, 20),
        ]);
    }
}
