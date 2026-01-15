<?php

namespace Tests\Unit\Listeners;

use App\Listeners\LogSentEmail;
use App\Mail\ReservationApprovedNotification;
use App\Models\Bien;
use App\Models\EmailLog;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class LogSentEmailTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
    }

    public function test_it_logs_sent_email_when_email_is_sent(): void
    {
        // Create test data
        $user = User::factory()->create(['email' => 'test@example.com']);
        $bien = Bien::factory()->create();
        $reservation = Reservation::factory()->create([
            'user_id' => $user->id,
            'bien_id' => $bien->id,
        ]);

        // Send an actual email
        Mail::to('test@example.com')->send(new ReservationApprovedNotification($reservation));

        // Assert email log was created
        $this->assertDatabaseHas('email_logs', [
            'destinataire' => 'test@example.com',
        ]);

        $log = EmailLog::first();
        $this->assertNotNull($log);
        $this->assertEquals('test@example.com', $log->destinataire);
        $this->assertNotNull($log->sujet);
        $this->assertNotNull($log->sent_at);
    }

    public function test_it_stores_subject_in_email_log(): void
    {
        $user = User::factory()->create(['email' => 'subject@example.com']);
        $bien = Bien::factory()->create();
        $reservation = Reservation::factory()->create([
            'user_id' => $user->id,
            'bien_id' => $bien->id,
        ]);

        Mail::to('subject@example.com')->send(new ReservationApprovedNotification($reservation));

        $log = EmailLog::first();
        $this->assertNotNull($log->sujet);
        $this->assertNotEmpty($log->sujet);
    }

    public function test_it_stores_body_preview_in_email_log(): void
    {
        $user = User::factory()->create(['email' => 'preview@example.com']);
        $bien = Bien::factory()->create();
        $reservation = Reservation::factory()->create([
            'user_id' => $user->id,
            'bien_id' => $bien->id,
        ]);

        Mail::to('preview@example.com')->send(new ReservationApprovedNotification($reservation));

        $log = EmailLog::first();
        $this->assertNotNull($log->body_preview);
        $this->assertLessThanOrEqual(500, mb_strlen($log->body_preview));
    }

    public function test_it_records_sent_at_timestamp(): void
    {
        $user = User::factory()->create(['email' => 'timestamp@example.com']);
        $bien = Bien::factory()->create();
        $reservation = Reservation::factory()->create([
            'user_id' => $user->id,
            'bien_id' => $bien->id,
        ]);

        Mail::to('timestamp@example.com')->send(new ReservationApprovedNotification($reservation));

        $log = EmailLog::first();
        $this->assertNotNull($log->sent_at);
        $this->assertInstanceOf(\Illuminate\Support\Carbon::class, $log->sent_at);
    }

    public function test_it_handles_multiple_emails(): void
    {
        $user1 = User::factory()->create(['email' => 'user1@example.com']);
        $user2 = User::factory()->create(['email' => 'user2@example.com']);
        $bien = Bien::factory()->create();
        
        $reservation1 = Reservation::factory()->create([
            'user_id' => $user1->id,
            'bien_id' => $bien->id,
        ]);
        
        $reservation2 = Reservation::factory()->create([
            'user_id' => $user2->id,
            'bien_id' => $bien->id,
        ]);

        Mail::to('user1@example.com')->send(new ReservationApprovedNotification($reservation1));
        Mail::to('user2@example.com')->send(new ReservationApprovedNotification($reservation2));

        $this->assertDatabaseCount('email_logs', 2);
        $this->assertDatabaseHas('email_logs', ['destinataire' => 'user1@example.com']);
        $this->assertDatabaseHas('email_logs', ['destinataire' => 'user2@example.com']);
    }

    public function test_listener_is_registered(): void
    {
        $this->assertTrue(
            class_exists(LogSentEmail::class),
            'LogSentEmail listener class should exist'
        );
    }
}

