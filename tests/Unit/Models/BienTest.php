<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\Bien;
use App\Models\User;
use App\Models\Reservation;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BienTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_be_created_with_fillable_attributes()
    {
        $bien = Bien::factory()->create([
            'name' => 'Test Property',
            'capacity' => 8,
            'description' => 'A beautiful test property',
        ]);

        $this->assertEquals('Test Property', $bien->name);
        $this->assertEquals(8, $bien->capacity);
        $this->assertEquals('A beautiful test property', $bien->description);
    }

    /** @test */
    public function it_has_many_users_with_pivot()
    {
        $bien = Bien::factory()->create();
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $bien->users()->attach($user1->id, ['profile' => 'gestionnaire']);
        $bien->users()->attach($user2->id, ['profile' => 'utilisateur']);

        $this->assertCount(2, $bien->users);
        $this->assertInstanceOf(User::class, $bien->users->first());
        $this->assertEquals('gestionnaire', $bien->users->first()->pivot->profile);
    }

    /** @test */
    public function it_has_many_reservations()
    {
        $bien = Bien::factory()->create();
        
        Reservation::factory()->count(3)->create([
            'bien_id' => $bien->id,
        ]);

        $this->assertCount(3, $bien->reservations);
        $this->assertInstanceOf(Reservation::class, $bien->reservations->first());
    }

    /** @test */
    public function it_can_have_different_capacity_sizes()
    {
        $smallBien = Bien::factory()->small()->create();
        $largeBien = Bien::factory()->large()->create();

        $this->assertLessThanOrEqual(4, $smallBien->capacity);
        $this->assertGreaterThanOrEqual(2, $smallBien->capacity);
        
        $this->assertGreaterThanOrEqual(10, $largeBien->capacity);
        $this->assertLessThanOrEqual(20, $largeBien->capacity);
    }
}
