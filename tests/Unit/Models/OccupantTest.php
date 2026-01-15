<?php

namespace Tests\Unit\Models;

use App\Models\Occupant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OccupantTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_can_create_an_occupant(): void
    {
        $occupant = Occupant::create([
            'name' => 'John Doe',
        ]);

        $this->assertInstanceOf(Occupant::class, $occupant);
        $this->assertEquals('John Doe', $occupant->name);
        $this->assertDatabaseHas('occupants', [
            'name' => 'John Doe',
        ]);
    }

    public function test_it_has_fillable_name_attribute(): void
    {
        $occupant = new Occupant();
        $fillable = $occupant->getFillable();

        $this->assertContains('name', $fillable);
    }

    public function test_it_can_update_occupant_name(): void
    {
        $occupant = Occupant::create([
            'name' => 'Original Name',
        ]);

        $occupant->update([
            'name' => 'Updated Name',
        ]);

        $this->assertEquals('Updated Name', $occupant->fresh()->name);
        $this->assertDatabaseHas('occupants', [
            'name' => 'Updated Name',
        ]);
    }

    public function test_it_can_delete_an_occupant(): void
    {
        $occupant = Occupant::create([
            'name' => 'To Be Deleted',
        ]);

        $id = $occupant->id;
        $occupant->delete();

        $this->assertDatabaseMissing('occupants', [
            'id' => $id,
        ]);
    }

    public function test_it_has_timestamps(): void
    {
        $occupant = Occupant::create([
            'name' => 'Test Occupant',
        ]);

        $this->assertNotNull($occupant->created_at);
        $this->assertNotNull($occupant->updated_at);
    }

    public function test_it_can_mass_assign_name(): void
    {
        $data = ['name' => 'Mass Assigned Name'];
        $occupant = Occupant::create($data);

        $this->assertEquals('Mass Assigned Name', $occupant->name);
    }

    public function test_it_uses_has_factory_trait(): void
    {
        $this->assertTrue(
            method_exists(Occupant::class, 'factory'),
            'Occupant should use HasFactory trait'
        );
    }

    public function test_it_can_retrieve_all_occupants(): void
    {
        Occupant::create(['name' => 'Occupant 1']);
        Occupant::create(['name' => 'Occupant 2']);
        Occupant::create(['name' => 'Occupant 3']);

        $occupants = Occupant::all();

        $this->assertCount(3, $occupants);
    }

    public function test_it_can_find_occupant_by_id(): void
    {
        $occupant = Occupant::create(['name' => 'Findable Occupant']);

        $found = Occupant::find($occupant->id);

        $this->assertNotNull($found);
        $this->assertEquals('Findable Occupant', $found->name);
    }

    public function test_it_can_find_occupant_by_name(): void
    {
        Occupant::create(['name' => 'Unique Name']);

        $found = Occupant::where('name', 'Unique Name')->first();

        $this->assertNotNull($found);
        $this->assertEquals('Unique Name', $found->name);
    }
}
