<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\User;
use App\Models\Bien;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;

class UserTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_has_many_biens_with_pivot()
    {
        $user = User::factory()->create();
        $bien1 = Bien::factory()->create();
        $bien2 = Bien::factory()->create();

        $user->biens()->attach($bien1->id, ['profile' => 'gestionnaire']);
        $user->biens()->attach($bien2->id, ['profile' => 'utilisateur']);

        $this->assertCount(2, $user->biens);
        $this->assertEquals('gestionnaire', $user->biens->first()->pivot->profile);
        $this->assertEquals('utilisateur', $user->biens->last()->pivot->profile);
    }

    /** @test */
    public function can_access_panel_returns_true_for_admin()
    {
        $user = User::factory()->create();
        Role::firstOrCreate(['name' => 'admin']);
        $user->assignRole('admin');

        $panel = \Mockery::mock(\Filament\Panel::class);

        $this->assertTrue($user->canAccessPanel($panel));
    }

    /** @test */
    public function can_access_panel_returns_true_for_user_role()
    {
        $user = User::factory()->create();
        Role::firstOrCreate(['name' => 'user']);
        $user->assignRole('user');

        $panel = \Mockery::mock(\Filament\Panel::class);

        $this->assertTrue($user->canAccessPanel($panel));
    }

    /** @test */
    public function can_access_panel_returns_false_for_no_role()
    {
        $user = User::factory()->create();

        $panel = \Mockery::mock(\Filament\Panel::class);

        $this->assertFalse($user->canAccessPanel($panel));
    }

    /** @test */
    public function get_accessible_biens_returns_all_for_admin()
    {
        $user = User::factory()->create();
        Role::firstOrCreate(['name' => 'admin']);
        $user->assignRole('admin');

        Bien::factory()->count(5)->create();

        $accessibleBiens = $user->getAccessibleBiens();

        $this->assertCount(5, $accessibleBiens);
    }

    /** @test */
    public function get_accessible_biens_returns_only_user_biens_for_non_admin()
    {
        $user = User::factory()->create();
        Role::firstOrCreate(['name' => 'user']);
        $user->assignRole('user');

        $userBien1 = Bien::factory()->create();
        $userBien2 = Bien::factory()->create();
        $otherBien = Bien::factory()->create();

        $user->biens()->attach($userBien1->id, ['profile' => 'gestionnaire']);
        $user->biens()->attach($userBien2->id, ['profile' => 'utilisateur']);

        $accessibleBiens = $user->getAccessibleBiens();

        $this->assertCount(2, $accessibleBiens);
        $this->assertTrue($accessibleBiens->contains($userBien1));
        $this->assertTrue($accessibleBiens->contains($userBien2));
        $this->assertFalse($accessibleBiens->contains($otherBien));
    }

    /** @test */
    public function password_is_hashed()
    {
        $user = User::factory()->create([
            'password' => 'plain-password',
        ]);

        $this->assertNotEquals('plain-password', $user->password);
        $this->assertTrue(\Hash::check('plain-password', $user->password));
    }

    /** @test */
    public function password_is_hidden_from_array()
    {
        $user = User::factory()->create();

        $userArray = $user->toArray();

        $this->assertArrayNotHasKey('password', $userArray);
        $this->assertArrayNotHasKey('remember_token', $userArray);
    }
}
