<?php

namespace Tests\Unit\Mail;

use App\Mail\ReservationPendingSelfManagerNotification;
use App\Models\Bien;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReservationPendingSelfManagerNotificationTest extends TestCase
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
        $mailable = new ReservationPendingSelfManagerNotification($this->reservation);

        $this->assertInstanceOf(ReservationPendingSelfManagerNotification::class, $mailable);
    }

    public function test_it_has_correct_subject(): void
    {
        $mailable = new ReservationPendingSelfManagerNotification($this->reservation);
        $envelope = $mailable->envelope();

        $this->assertEquals(
            __('filament.emails.reservation_pending_self_manager.subject'),
            $envelope->subject
        );
    }

    public function test_it_uses_correct_view(): void
    {
        $mailable = new ReservationPendingSelfManagerNotification($this->reservation);
        $content = $mailable->content();

        $this->assertEquals('emails.reservation-pending-self-manager', $content->view);
    }

    public function test_it_has_no_attachments(): void
    {
        $mailable = new ReservationPendingSelfManagerNotification($this->reservation);
        $attachments = $mailable->attachments();

        $this->assertIsArray($attachments);
        $this->assertEmpty($attachments);
    }

    public function test_it_contains_reservation_data(): void
    {
        $mailable = new ReservationPendingSelfManagerNotification($this->reservation);

        $this->assertEquals($this->reservation->id, $mailable->reservation->id);
    }

    public function test_it_can_be_rendered(): void
    {
        $mailable = new ReservationPendingSelfManagerNotification($this->reservation);
        
        $rendered = $mailable->render();

        $this->assertIsString($rendered);
        $this->assertNotEmpty($rendered);
    }
}
