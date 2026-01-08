<?php

namespace Database\Factories;

use App\Models\ReservationStatusHistory;
use App\Models\Reservation;
use App\Models\User;
use App\Enums\ReservationStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ReservationStatusHistory>
 */
class ReservationStatusHistoryFactory extends Factory
{
    protected $model = ReservationStatusHistory::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'reservation_id' => Reservation::factory(),
            'status' => fake()->randomElement(ReservationStatus::cases()),
            'user_id' => User::factory(),
            'comment' => fake()->optional()->sentence(),
        ];
    }

    /**
     * Indicate that the status is accepted.
     */
    public function accepted(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => ReservationStatus::Accepte,
        ]);
    }

    /**
     * Indicate that the status is refused.
     */
    public function refused(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => ReservationStatus::Refuse,
        ]);
    }

    /**
     * Indicate that the status is pending.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => ReservationStatus::EnAttente,
        ]);
    }
}
