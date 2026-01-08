<?php

namespace Database\Factories;

use App\Models\Reservation;
use App\Models\User;
use App\Models\Bien;
use App\Models\Occupant;
use App\Enums\ReservationStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Reservation>
 */
class ReservationFactory extends Factory
{
    protected $model = Reservation::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startDate = fake()->dateTimeBetween('now', '+1 month');
        $endDate = fake()->dateTimeBetween($startDate, '+2 months');

        return [
            'user_id' => User::factory(),
            'bien_id' => Bien::factory(),
            'occupant_id' => Occupant::factory(),
            'number_of_guests' => fake()->numberBetween(1, 8),
            'date_start' => $startDate,
            'date_end' => $endDate,
            'comment' => fake()->optional()->sentence(),
            'status' => ReservationStatus::EnAttente,
            'reminder_sent_at' => null,
        ];
    }

    /**
     * Indicate that the reservation is accepted.
     */
    public function accepted(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => ReservationStatus::Accepte,
        ]);
    }

    /**
     * Indicate that the reservation is refused.
     */
    public function refused(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => ReservationStatus::Refuse,
        ]);
    }

    /**
     * Indicate that the reservation is pending.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => ReservationStatus::EnAttente,
        ]);
    }

    /**
     * Indicate that the reservation starts tomorrow.
     */
    public function startingTomorrow(): static
    {
        $tomorrow = now()->addDay()->startOfDay();
        return $this->state(fn (array $attributes) => [
            'date_start' => $tomorrow,
            'date_end' => $tomorrow->copy()->addDays(3),
        ]);
    }

    /**
     * Indicate that the reservation is current (ongoing).
     */
    public function current(): static
    {
        return $this->state(fn (array $attributes) => [
            'date_start' => now()->subDays(2),
            'date_end' => now()->addDays(2),
        ]);
    }

    /**
     * Indicate that the reservation is in the past.
     */
    public function past(): static
    {
        return $this->state(fn (array $attributes) => [
            'date_start' => now()->subDays(10),
            'date_end' => now()->subDays(5),
        ]);
    }

    /**
     * Indicate that a reminder has been sent.
     */
    public function reminderSent(): static
    {
        return $this->state(fn (array $attributes) => [
            'reminder_sent_at' => now(),
        ]);
    }
}
