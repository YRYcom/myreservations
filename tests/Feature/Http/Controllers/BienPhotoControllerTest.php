<?php

namespace Tests\Feature\Http\Controllers;

use App\Enums\ProfileType;
use App\Models\Bien;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class BienPhotoControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    /** @test */
    public function unauthenticated_user_cannot_access_photo()
    {
        $bien = Bien::factory()->create(['photo' => 'biens/test.jpg']);
        
        // Créer un faux fichier
        Storage::disk('public')->put('biens/test.jpg', 'fake image content');

        $response = $this->get(route('bien.photo', $bien));

        $response->assertRedirect(route('filament.home.auth.login'));
    }

    /** @test */
    public function admin_can_access_any_photo()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        
        $bien = Bien::factory()->create(['photo' => 'biens/test.jpg']);
        Storage::disk('public')->put('biens/test.jpg', 'fake image content');

        $response = $this->actingAs($admin)->get(route('bien.photo', $bien));

        $response->assertOk();
        $response->assertHeader('Content-Type', 'image/jpeg');
    }

    /** @test */
    public function manager_can_access_their_bien_photo()
    {
        $manager = User::factory()->create();
        $manager->assignRole('user');
        
        $bien = Bien::factory()->create(['photo' => 'biens/test.jpg']);
        
        // Attacher le gestionnaire au bien
        $bien->users()->attach($manager->id, ['profile' => ProfileType::Gestionnaire->value]);
        
        Storage::disk('public')->put('biens/test.jpg', 'fake image content');

        $response = $this->actingAs($manager)->get(route('bien.photo', $bien));

        $response->assertOk();
    }

    /** @test */
    public function manager_cannot_access_other_bien_photo()
    {
        $manager = User::factory()->create();
        $manager->assignRole('user');
        
        $myBien = Bien::factory()->create();
        $myBien->users()->attach($manager->id, ['profile' => ProfileType::Gestionnaire->value]);
        
        $otherBien = Bien::factory()->create(['photo' => 'biens/other.jpg']);
        Storage::disk('public')->put('biens/other.jpg', 'fake image content');

        $response = $this->actingAs($manager)->get(route('bien.photo', $otherBien));

        $response->assertForbidden();
    }

    /** @test */
    public function user_can_access_photo_of_their_reservation()
    {
        $user = User::factory()->create();
        $user->assignRole('user');
        
        $bien = Bien::factory()->create(['photo' => 'biens/test.jpg']);
        
        // Créer une réservation pour cet utilisateur
        Reservation::factory()->create([
            'user_id' => $user->id,
            'bien_id' => $bien->id,
        ]);
        
        Storage::disk('public')->put('biens/test.jpg', 'fake image content');

        $response = $this->actingAs($user)->get(route('bien.photo', $bien));

        $response->assertOk();
    }

    /** @test */
    public function user_cannot_access_photo_without_reservation()
    {
        $user = User::factory()->create();
        $user->assignRole('user');
        
        $bien = Bien::factory()->create(['photo' => 'biens/test.jpg']);
        Storage::disk('public')->put('biens/test.jpg', 'fake image content');

        $response = $this->actingAs($user)->get(route('bien.photo', $bien));

        $response->assertForbidden();
    }

    /** @test */
    public function returns_404_when_bien_has_no_photo()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        
        $bien = Bien::factory()->create(['photo' => null]);

        $response = $this->actingAs($admin)->get(route('bien.photo', $bien));

        $response->assertNotFound();
    }

    /** @test */
    public function returns_404_when_photo_file_does_not_exist()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        
        $bien = Bien::factory()->create(['photo' => 'biens/nonexistent.jpg']);

        $response = $this->actingAs($admin)->get(route('bien.photo', $bien));

        $response->assertNotFound();
    }

    /** @test */
    public function returns_correct_cache_headers()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        
        $bien = Bien::factory()->create(['photo' => 'biens/test.jpg']);
        Storage::disk('public')->put('biens/test.jpg', 'fake image content');

        $response = $this->actingAs($admin)->get(route('bien.photo', $bien));

        $response->assertOk();
        $response->assertHeader('Cache-Control');
        $response->assertHeader('ETag');
        $response->assertHeader('Last-Modified');
    }

    /** @test */
    public function returns_304_when_etag_matches()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        
        $bien = Bien::factory()->create(['photo' => 'biens/test.jpg']);
        Storage::disk('public')->put('biens/test.jpg', 'fake image content');

        // Première requête pour obtenir l'ETag
        $firstResponse = $this->actingAs($admin)->get(route('bien.photo', $bien));
        $etag = $firstResponse->headers->get('ETag');

        // Deuxième requête avec l'ETag
        $response = $this->actingAs($admin)
            ->withHeaders(['If-None-Match' => $etag])
            ->get(route('bien.photo', $bien));

        $response->assertStatus(304);
    }
}
