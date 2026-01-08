<?php

namespace Tests\Unit\Mail;

use Tests\TestCase;
use App\Models\Reservation;
use App\Models\User;
use App\Models\Bien;
use App\Mail\ReservationReminderNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ReservationReminderNotificationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_be_constructed_with_a_reservation()
    {
        $reservation = Reservation::factory()->create();

        $mail = new ReservationReminderNotification($reservation);

        $this->assertInstanceOf(ReservationReminderNotification::class, $mail);
        $this->assertEquals($reservation->id, $mail->reservation->id);
    }

    /** @test */
    public function it_has_correct_subject()
    {
        $reservation = Reservation::factory()->create();
        $mail = new ReservationReminderNotification($reservation);

        $envelope = $mail->envelope();

        $this->assertNotEmpty($envelope->subject);
    }

    /** @test */
    public function it_uses_correct_view()
    {
        $reservation = Reservation::factory()->create();
        $mail = new ReservationReminderNotification($reservation);

        $content = $mail->content();

        $this->assertEquals('emails.reservation-reminder', $content->view);
    }

    /** @test */
    public function it_has_no_attachments()
    {
        $reservation = Reservation::factory()->create();
        $mail = new ReservationReminderNotification($reservation);

        $attachments = $mail->attachments();

        $this->assertIsArray($attachments);
        $this->assertEmpty($attachments);
    }

    /** @test */
    public function reservation_data_is_accessible_in_mail()
    {
        $user = User::factory()->create(['name' => 'John Doe']);
        $bien = Bien::factory()->create(['name' => 'Test Property']);
        
        $reservation = Reservation::factory()->create([
            'user_id' => $user->id,
            'bien_id' => $bien->id,
            'date_start' => now()->addDay(),
            'date_end' => now()->addDays(3),
        ]);

        $mail = new ReservationReminderNotification($reservation);

        $this->assertEquals('John Doe', $mail->reservation->user->name);
        $this->assertEquals('Test Property', $mail->reservation->bien->name);
    }
}
