<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\Reservation;
use App\Models\User;
use App\Models\Bien;
use App\Models\Occupant;
use App\Models\ReservationStatusHistory;
use App\Enums\ReservationStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ReservationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_belongs_to_a_user()
    {
        $user = User::factory()->create();
        $reservation = Reservation::factory()->create(['user_id' => $user->id]);

        $this->assertInstanceOf(User::class, $reservation->user);
        $this->assertEquals($user->id, $reservation->user->id);
    }

    /** @test */
    public function it_belongs_to_a_bien()
    {
        $bien = Bien::factory()->create();
        $reservation = Reservation::factory()->create(['bien_id' => $bien->id]);

        $this->assertInstanceOf(Bien::class, $reservation->bien);
        $this->assertEquals($bien->id, $reservation->bien->id);
    }

    /** @test */
    public function it_belongs_to_an_occupant()
    {
        $occupant = Occupant::factory()->create();
        $reservation = Reservation::factory()->create(['occupant_id' => $occupant->id]);

        $this->assertInstanceOf(Occupant::class, $reservation->occupant);
        $this->assertEquals($occupant->id, $reservation->occupant->id);
    }

    /** @test */
    public function it_has_many_status_history_entries()
    {
        $reservation = Reservation::factory()->create();
        
        ReservationStatusHistory::factory()->count(3)->create([
            'reservation_id' => $reservation->id,
        ]);

        $this->assertCount(3, $reservation->statusHistory);
        $this->assertInstanceOf(ReservationStatusHistory::class, $reservation->statusHistory->first());
    }

    /** @test */
    public function it_casts_dates_correctly()
    {
        $reservation = Reservation::factory()->create([
            'date_start' => '2026-01-15',
            'date_end' => '2026-01-20',
        ]);

        $this->assertInstanceOf(\Illuminate\Support\Carbon::class, $reservation->date_start);
        $this->assertInstanceOf(\Illuminate\Support\Carbon::class, $reservation->date_end);
    }

    /** @test */
    public function it_casts_status_to_enum()
    {
        $reservation = Reservation::factory()->create([
            'status' => ReservationStatus::Accepte,
        ]);

        $this->assertInstanceOf(ReservationStatus::class, $reservation->status);
        $this->assertEquals(ReservationStatus::Accepte, $reservation->status);
    }

    /** @test */
    public function scope_ordered_by_start_date_filters_past_reservations()
    {
        // Create past reservation
        Reservation::factory()->create([
            'date_start' => now()->subDays(10),
            'date_end' => now()->subDays(5),
        ]);

        // Create future reservations
        $futureReservation1 = Reservation::factory()->create([
            'date_start' => now()->addDays(5),
            'date_end' => now()->addDays(10),
        ]);

        $futureReservation2 = Reservation::factory()->create([
            'date_start' => now()->addDays(2),
            'date_end' => now()->addDays(4),
        ]);

        $results = Reservation::orderedByStartDate()->get();

        $this->assertCount(2, $results);
        $this->assertEquals($futureReservation2->id, $results->first()->id);
        $this->assertEquals($futureReservation1->id, $results->last()->id);
    }

    /** @test */
    public function scope_ordered_by_start_date_without_restrictions_includes_all()
    {
        $pastReservation = Reservation::factory()->create([
            'date_start' => now()->subDays(10),
            'date_end' => now()->subDays(5),
        ]);

        $futureReservation = Reservation::factory()->create([
            'date_start' => now()->addDays(5),
            'date_end' => now()->addDays(10),
        ]);

        $results = Reservation::orderedByStartDateWithoutRestrictions()->get();

        $this->assertCount(2, $results);
        $this->assertEquals($pastReservation->id, $results->first()->id);
    }

    /** @test */
    public function is_current_returns_true_when_reservation_is_ongoing()
    {
        $reservation = Reservation::factory()->current()->create();

        $this->assertTrue($reservation->isCurrent());
    }

    /** @test */
    public function is_current_returns_false_when_reservation_is_in_future()
    {
        $reservation = Reservation::factory()->create([
            'date_start' => now()->addDays(5),
            'date_end' => now()->addDays(10),
        ]);

        $this->assertFalse($reservation->isCurrent());
    }

    /** @test */
    public function is_current_returns_false_when_reservation_is_in_past()
    {
        $reservation = Reservation::factory()->past()->create();

        $this->assertFalse($reservation->isCurrent());
    }

    /** @test */
    public function can_be_approved_by_returns_true_for_manager()
    {
        $user = User::factory()->create();
        $bien = Bien::factory()->create();
        
        // Attach user as manager
        $bien->users()->attach($user->id, ['profile' => 'gestionnaire']);
        
        $reservation = Reservation::factory()->create(['bien_id' => $bien->id]);

        $this->assertTrue($reservation->canBeApprovedBy($user));
    }

    /** @test */
    public function can_be_approved_by_returns_false_for_non_manager()
    {
        $user = User::factory()->create();
        $bien = Bien::factory()->create();
        
        // Attach user as regular user
        $bien->users()->attach($user->id, ['profile' => 'utilisateur']);
        
        $reservation = Reservation::factory()->create(['bien_id' => $bien->id]);

        $this->assertFalse($reservation->canBeApprovedBy($user));
    }

    /** @test */
    public function can_be_approved_by_returns_false_for_unrelated_user()
    {
        $user = User::factory()->create();
        $bien = Bien::factory()->create();
        $reservation = Reservation::factory()->create(['bien_id' => $bien->id]);

        $this->assertFalse($reservation->canBeApprovedBy($user));
    }

    /** @test */
    public function approve_changes_status_to_accepted()
    {
        $reservation = Reservation::factory()->pending()->create();

        $reservation->approve();

        $this->assertEquals(ReservationStatus::Accepte, $reservation->fresh()->status);
    }

    /** @test */
    public function approve_creates_status_history_entry()
    {
        $user = User::factory()->create();
        $reservation = Reservation::factory()->pending()->create();

        $reservation->approve('Approved by manager', $user->id);

        $this->assertCount(1, $reservation->statusHistory);
        $history = $reservation->statusHistory->first();
        
        $this->assertEquals(ReservationStatus::Accepte, $history->status);
        $this->assertEquals('Approved by manager', $history->comment);
        $this->assertEquals($user->id, $history->user_id);
    }

    /** @test */
    public function reject_changes_status_to_refused()
    {
        $reservation = Reservation::factory()->pending()->create();

        $reservation->reject();

        $this->assertEquals(ReservationStatus::Refuse, $reservation->fresh()->status);
    }

    /** @test */
    public function reject_creates_status_history_entry()
    {
        $user = User::factory()->create();
        $reservation = Reservation::factory()->pending()->create();

        $reservation->reject('Not available', $user->id);

        $this->assertCount(1, $reservation->statusHistory);
        $history = $reservation->statusHistory->first();
        
        $this->assertEquals(ReservationStatus::Refuse, $history->status);
        $this->assertEquals('Not available', $history->comment);
        $this->assertEquals($user->id, $history->user_id);
    }

    /** @test */
    public function reset_to_pending_changes_status_to_pending()
    {
        $reservation = Reservation::factory()->accepted()->create();

        $reservation->resetToPending();

        $this->assertEquals(ReservationStatus::EnAttente, $reservation->fresh()->status);
    }

    /** @test */
    public function reset_to_pending_creates_status_history_entry()
    {
        $user = User::factory()->create();
        $reservation = Reservation::factory()->accepted()->create();

        $reservation->resetToPending('Reset by admin', $user->id);

        $this->assertCount(1, $reservation->statusHistory);
        $history = $reservation->statusHistory->first();
        
        $this->assertEquals(ReservationStatus::EnAttente, $history->status);
        $this->assertEquals('Reset by admin', $history->comment);
        $this->assertEquals($user->id, $history->user_id);
    }

    /** @test */
    public function log_status_change_creates_history_entry()
    {
        $user = User::factory()->create();
        $reservation = Reservation::factory()->create();

        $reservation->logStatusChange(ReservationStatus::Accepte, 'Test comment', $user->id);

        $this->assertCount(1, $reservation->statusHistory);
        $history = $reservation->statusHistory->first();
        
        $this->assertEquals(ReservationStatus::Accepte, $history->status);
        $this->assertEquals('Test comment', $history->comment);
        $this->assertEquals($user->id, $history->user_id);
        $this->assertEquals($reservation->id, $history->reservation_id);
    }

    /** @test */
    public function log_status_change_works_without_comment_and_user()
    {
        $reservation = Reservation::factory()->create();

        $reservation->logStatusChange(ReservationStatus::Accepte);

        $this->assertCount(1, $reservation->statusHistory);
        $history = $reservation->statusHistory->first();
        
        $this->assertEquals(ReservationStatus::Accepte, $history->status);
        $this->assertNull($history->comment);
        $this->assertNull($history->user_id);
    }
}
