<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\ReservationStatusHistory;
use App\Models\Reservation;
use App\Models\User;
use App\Enums\ReservationStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ReservationStatusHistoryTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_be_created_with_fillable_attributes()
    {
        $reservation = Reservation::factory()->create();
        $user = User::factory()->create();

        $history = ReservationStatusHistory::create([
            'reservation_id' => $reservation->id,
            'status' => ReservationStatus::Accepte,
            'user_id' => $user->id,
            'comment' => 'Test comment',
        ]);

        $this->assertEquals($reservation->id, $history->reservation_id);
        $this->assertEquals(ReservationStatus::Accepte, $history->status);
        $this->assertEquals($user->id, $history->user_id);
        $this->assertEquals('Test comment', $history->comment);
    }

    /** @test */
    public function it_belongs_to_a_reservation()
    {
        $reservation = Reservation::factory()->create();
        $history = ReservationStatusHistory::factory()->create([
            'reservation_id' => $reservation->id,
        ]);

        $this->assertInstanceOf(Reservation::class, $history->reservation);
        $this->assertEquals($reservation->id, $history->reservation->id);
    }

    /** @test */
    public function it_belongs_to_a_user()
    {
        $user = User::factory()->create();
        $history = ReservationStatusHistory::factory()->create([
            'user_id' => $user->id,
        ]);

        $this->assertInstanceOf(User::class, $history->user);
        $this->assertEquals($user->id, $history->user->id);
    }

    /** @test */
    public function it_can_have_null_user()
    {
        $history = ReservationStatusHistory::factory()->create([
            'user_id' => null,
        ]);

        $this->assertNull($history->user_id);
        $this->assertNull($history->user);
    }

    /** @test */
    public function it_casts_status_to_enum()
    {
        $history = ReservationStatusHistory::factory()->create([
            'status' => ReservationStatus::Accepte,
        ]);

        $this->assertInstanceOf(ReservationStatus::class, $history->status);
        $this->assertEquals(ReservationStatus::Accepte, $history->status);
    }

    /** @test */
    public function it_can_create_history_for_all_status_types()
    {
        $reservation = Reservation::factory()->create();

        $pendingHistory = ReservationStatusHistory::factory()->pending()->create([
            'reservation_id' => $reservation->id,
        ]);
        $acceptedHistory = ReservationStatusHistory::factory()->accepted()->create([
            'reservation_id' => $reservation->id,
        ]);
        $refusedHistory = ReservationStatusHistory::factory()->refused()->create([
            'reservation_id' => $reservation->id,
        ]);

        $this->assertEquals(ReservationStatus::EnAttente, $pendingHistory->status);
        $this->assertEquals(ReservationStatus::Accepte, $acceptedHistory->status);
        $this->assertEquals(ReservationStatus::Refuse, $refusedHistory->status);
    }
}
