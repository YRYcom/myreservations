<?php

namespace Tests\Unit\Mail;

use Tests\TestCase;
use App\Models\Reservation;
use App\Models\User;
use App\Models\Bien;
use App\Mail\ReservationRejectedNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ReservationRejectedNotificationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_be_constructed_with_a_reservation()
    {
        $reservation = Reservation::factory()->create();

        $mail = new ReservationRejectedNotification($reservation);

        $this->assertInstanceOf(ReservationRejectedNotification::class, $mail);
        $this->assertEquals($reservation->id, $mail->reservation->id);
    }

    /** @test */
    public function it_can_be_constructed_with_a_comment()
    {
        $reservation = Reservation::factory()->create();
        $comment = 'Property not available on those dates';

        $mail = new ReservationRejectedNotification($reservation, $comment);

        $this->assertEquals($comment, $mail->comment);
    }

    /** @test */
    public function it_can_be_constructed_without_a_comment()
    {
        $reservation = Reservation::factory()->create();

        $mail = new ReservationRejectedNotification($reservation);

        $this->assertNull($mail->comment);
    }

    /** @test */
    public function it_has_correct_subject()
    {
        $reservation = Reservation::factory()->create();
        $mail = new ReservationRejectedNotification($reservation);

        $envelope = $mail->envelope();

        $this->assertNotEmpty($envelope->subject);
    }

    /** @test */
    public function it_uses_correct_view()
    {
        $reservation = Reservation::factory()->create();
        $mail = new ReservationRejectedNotification($reservation);

        $content = $mail->content();

        $this->assertEquals('emails.reservation-rejected', $content->view);
    }

    /** @test */
    public function it_has_no_attachments()
    {
        $reservation = Reservation::factory()->create();
        $mail = new ReservationRejectedNotification($reservation);

        $attachments = $mail->attachments();

        $this->assertIsArray($attachments);
        $this->assertEmpty($attachments);
    }

    /** @test */
    public function reservation_and_comment_data_are_accessible()
    {
        $user = User::factory()->create(['name' => 'Bob Smith']);
        $bien = Bien::factory()->create(['name' => 'Rejected Property']);
        
        $reservation = Reservation::factory()->create([
            'user_id' => $user->id,
            'bien_id' => $bien->id,
        ]);

        $comment = 'Sorry, fully booked';
        $mail = new ReservationRejectedNotification($reservation, $comment);

        $this->assertEquals('Bob Smith', $mail->reservation->user->name);
        $this->assertEquals('Rejected Property', $mail->reservation->bien->name);
        $this->assertEquals($comment, $mail->comment);
    }
}
