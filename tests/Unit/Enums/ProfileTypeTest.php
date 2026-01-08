<?php

namespace Tests\Unit\Enums;

use Tests\TestCase;
use App\Enums\ProfileType;

class ProfileTypeTest extends TestCase
{
    /** @test */
    public function it_has_two_cases()
    {
        $cases = ProfileType::cases();

        $this->assertCount(2, $cases);
        $this->assertContains(ProfileType::Utilisateur, $cases);
        $this->assertContains(ProfileType::Gestionnaire, $cases);
    }

    /** @test */
    public function it_has_correct_values()
    {
        $this->assertEquals('utilisateur', ProfileType::Utilisateur->value);
        $this->assertEquals('gestionnaire', ProfileType::Gestionnaire->value);
    }

    /** @test */
    public function label_returns_translated_string_for_utilisateur()
    {
        $label = ProfileType::Utilisateur->label();

        $this->assertIsString($label);
        $this->assertNotEmpty($label);
    }

    /** @test */
    public function label_returns_translated_string_for_gestionnaire()
    {
        $label = ProfileType::Gestionnaire->label();

        $this->assertIsString($label);
        $this->assertNotEmpty($label);
    }

    /** @test */
    public function options_returns_array_with_all_cases()
    {
        $options = ProfileType::options();

        $this->assertIsArray($options);
        $this->assertCount(2, $options);
        $this->assertArrayHasKey('utilisateur', $options);
        $this->assertArrayHasKey('gestionnaire', $options);
    }

    /** @test */
    public function options_values_are_labels()
    {
        $options = ProfileType::options();

        $this->assertEquals(ProfileType::Utilisateur->label(), $options['utilisateur']);
        $this->assertEquals(ProfileType::Gestionnaire->label(), $options['gestionnaire']);
    }
}
