<?php

namespace Tests\Unit\Mail;

use App\Mail\ManagerApprovedReservationNotification;
use App\Models\Bien;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ManagerApprovedReservationNotificationTest extends TestCase
{
    use RefreshDatabase;

    private Reservation $reservation;

    protected function setUp(): void
    {
        parent::setUp();

        $user = User::factory()->create();
        $bien = Bien::factory()->create();
        
        $this->reservation = Reservation::factory()->create([
            'user_id' => $user->id,
            'bien_id' => $bien->id,
        ]);
    }

    public function test_it_can_be_instantiated(): void
    {
        $mailable = new ManagerApprovedReservationNotification($this->reservation);

        $this->assertInstanceOf(ManagerApprovedReservationNotification::class, $mailable);
    }

    public function test_it_has_correct_subject(): void
    {
        $mailable = new ManagerApprovedReservationNotification($this->reservation);
        $envelope = $mailable->envelope();

        $this->assertEquals(
            __('filament.emails.manager_approved_reservation.subject'),
            $envelope->subject
        );
    }

    public function test_it_uses_correct_view(): void
    {
        $mailable = new ManagerApprovedReservationNotification($this->reservation);
        $content = $mailable->content();

        $this->assertEquals('emails.manager-approved-reservation', $content->view);
    }

    public function test_it_has_no_attachments(): void
    {
        $mailable = new ManagerApprovedReservationNotification($this->reservation);
        $attachments = $mailable->attachments();

        $this->assertIsArray($attachments);
        $this->assertEmpty($attachments);
    }

    public function test_it_contains_reservation_data(): void
    {
        $mailable = new ManagerApprovedReservationNotification($this->reservation);

        $this->assertEquals($this->reservation->id, $mailable->reservation->id);
    }

    public function test_it_can_be_rendered(): void
    {
        $mailable = new ManagerApprovedReservationNotification($this->reservation);
        
        $rendered = $mailable->render();

        $this->assertIsString($rendered);
        $this->assertNotEmpty($rendered);
    }
}
