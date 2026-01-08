<?php

namespace Tests\Unit\Mail;

use Tests\TestCase;
use App\Models\Reservation;
use App\Models\User;
use App\Models\Bien;
use App\Mail\ReservationPendingUserNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ReservationPendingUserNotificationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_be_constructed_with_a_reservation()
    {
        $reservation = Reservation::factory()->create();

        $mail = new ReservationPendingUserNotification($reservation);

        $this->assertInstanceOf(ReservationPendingUserNotification::class, $mail);
        $this->assertEquals($reservation->id, $mail->reservation->id);
    }

    /** @test */
    public function it_has_correct_subject()
    {
        $reservation = Reservation::factory()->create();
        $mail = new ReservationPendingUserNotification($reservation);

        $envelope = $mail->envelope();

        $this->assertNotEmpty($envelope->subject);
    }

    /** @test */
    public function it_uses_correct_view()
    {
        $reservation = Reservation::factory()->create();
        $mail = new ReservationPendingUserNotification($reservation);

        $content = $mail->content();

        $this->assertEquals('emails.reservation-pending-user', $content->view);
    }

    /** @test */
    public function it_has_no_attachments()
    {
        $reservation = Reservation::factory()->create();
        $mail = new ReservationPendingUserNotification($reservation);

        $attachments = $mail->attachments();

        $this->assertIsArray($attachments);
        $this->assertEmpty($attachments);
    }

    /** @test */
    public function reservation_data_is_accessible_in_mail()
    {
        $user = User::factory()->create(['name' => 'Alice Johnson']);
        $bien = Bien::factory()->create(['name' => 'Pending Property']);
        
        $reservation = Reservation::factory()->pending()->create([
            'user_id' => $user->id,
            'bien_id' => $bien->id,
            'date_start' => now()->addWeek(),
            'date_end' => now()->addWeek()->addDays(5),
        ]);

        $mail = new ReservationPendingUserNotification($reservation);

        $this->assertEquals('Alice Johnson', $mail->reservation->user->name);
        $this->assertEquals('Pending Property', $mail->reservation->bien->name);
    }
}
