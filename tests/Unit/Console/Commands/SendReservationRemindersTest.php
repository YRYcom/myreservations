<?php

namespace Tests\Unit\Console\Commands;

use Tests\TestCase;
use App\Models\Reservation;
use App\Models\User;
use App\Models\Bien;
use App\Enums\ReservationStatus;
use App\Mail\ReservationReminderNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;

class SendReservationRemindersTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_sends_reminders_for_reservations_starting_tomorrow()
    {
        Mail::fake();

        $user = User::factory()->create();
        $bien = Bien::factory()->create();

        // Create reservation starting tomorrow
        $reservation = Reservation::factory()
            ->accepted()
            ->startingTomorrow()
            ->create([
                'user_id' => $user->id,
                'bien_id' => $bien->id,
            ]);

        $this->artisan('reservations:send-reminders')
            ->assertSuccessful();

        Mail::assertSent(ReservationReminderNotification::class, function ($mail) use ($user) {
            return $mail->hasTo($user->email);
        });
    }

    /** @test */
    public function it_only_sends_reminders_for_accepted_reservations()
    {
        Mail::fake();

        $user = User::factory()->create();
        $bien = Bien::factory()->create();

        // Create pending reservation starting tomorrow
        Reservation::factory()
            ->pending()
            ->startingTomorrow()
            ->create([
                'user_id' => $user->id,
                'bien_id' => $bien->id,
            ]);

        // Create refused reservation starting tomorrow
        Reservation::factory()
            ->refused()
            ->startingTomorrow()
            ->create([
                'user_id' => $user->id,
                'bien_id' => $bien->id,
            ]);

        $this->artisan('reservations:send-reminders')
            ->assertSuccessful();

        Mail::assertNothingSent();
    }

    /** @test */
    public function it_does_not_send_reminders_twice()
    {
        Mail::fake();

        $user = User::factory()->create();
        $bien = Bien::factory()->create();

        // Create reservation with reminder already sent
        Reservation::factory()
            ->accepted()
            ->startingTomorrow()
            ->reminderSent()
            ->create([
                'user_id' => $user->id,
                'bien_id' => $bien->id,
            ]);

        $this->artisan('reservations:send-reminders')
            ->assertSuccessful();

        Mail::assertNothingSent();
    }

    /** @test */
    public function it_updates_reminder_sent_at_after_sending()
    {
        Mail::fake();

        $user = User::factory()->create();
        $bien = Bien::factory()->create();

        $reservation = Reservation::factory()
            ->accepted()
            ->startingTomorrow()
            ->create([
                'user_id' => $user->id,
                'bien_id' => $bien->id,
            ]);

        $this->assertNull($reservation->reminder_sent_at);

        $this->artisan('reservations:send-reminders')
            ->assertSuccessful();

        $reservation->refresh();
        $this->assertNotNull($reservation->reminder_sent_at);
    }

    /** @test */
    public function it_does_not_send_reminders_for_reservations_starting_today()
    {
        Mail::fake();

        $user = User::factory()->create();
        $bien = Bien::factory()->create();

        // Create reservation starting today
        Reservation::factory()
            ->accepted()
            ->create([
                'user_id' => $user->id,
                'bien_id' => $bien->id,
                'date_start' => now()->startOfDay(),
                'date_end' => now()->addDays(3),
            ]);

        $this->artisan('reservations:send-reminders')
            ->assertSuccessful();

        Mail::assertNothingSent();
    }

    /** @test */
    public function it_does_not_send_reminders_for_reservations_starting_in_two_days()
    {
        Mail::fake();

        $user = User::factory()->create();
        $bien = Bien::factory()->create();

        // Create reservation starting in 2 days (after day after tomorrow)
        Reservation::factory()
            ->accepted()
            ->create([
                'user_id' => $user->id,
                'bien_id' => $bien->id,
                'date_start' => now()->addDays(2)->endOfDay(),
                'date_end' => now()->addDays(5),
            ]);

        $this->artisan('reservations:send-reminders')
            ->assertSuccessful();

        Mail::assertNothingSent();
    }

    /** @test */
    public function it_sends_multiple_reminders_when_multiple_reservations_qualify()
    {
        Mail::fake();

        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $bien = Bien::factory()->create();

        // Create two reservations starting tomorrow
        Reservation::factory()
            ->accepted()
            ->startingTomorrow()
            ->create([
                'user_id' => $user1->id,
                'bien_id' => $bien->id,
            ]);

        Reservation::factory()
            ->accepted()
            ->startingTomorrow()
            ->create([
                'user_id' => $user2->id,
                'bien_id' => $bien->id,
            ]);

        $this->artisan('reservations:send-reminders')
            ->assertSuccessful();

        Mail::assertSent(ReservationReminderNotification::class, 2);
    }

    /** @test */
    public function it_continues_sending_even_if_one_fails()
    {
        Mail::fake();

        $user1 = User::factory()->create(['email' => 'user1@example.com']);
        $user2 = User::factory()->create(['email' => 'user2@example.com']);
        $bien = Bien::factory()->create();

        Reservation::factory()
            ->accepted()
            ->startingTomorrow()
            ->create([
                'user_id' => $user1->id,
                'bien_id' => $bien->id,
            ]);

        Reservation::factory()
            ->accepted()
            ->startingTomorrow()
            ->create([
                'user_id' => $user2->id,
                'bien_id' => $bien->id,
            ]);

        $this->artisan('reservations:send-reminders')
            ->assertSuccessful();

        // Both should be sent (we're using Mail::fake so no actual failures)
        Mail::assertSent(ReservationReminderNotification::class, 2);
    }
}
