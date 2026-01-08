<?php

namespace Tests\Unit\Enums;

use Tests\TestCase;
use App\Enums\ReservationStatus;

class ReservationStatusTest extends TestCase
{
    /** @test */
    public function it_has_three_cases()
    {
        $cases = ReservationStatus::cases();

        $this->assertCount(3, $cases);
        $this->assertContains(ReservationStatus::EnAttente, $cases);
        $this->assertContains(ReservationStatus::Accepte, $cases);
        $this->assertContains(ReservationStatus::Refuse, $cases);
    }

    /** @test */
    public function it_has_correct_values()
    {
        $this->assertEquals('en_attente', ReservationStatus::EnAttente->value);
        $this->assertEquals('accepte', ReservationStatus::Accepte->value);
        $this->assertEquals('refuse', ReservationStatus::Refuse->value);
    }

    /** @test */
    public function label_returns_translated_string_for_en_attente()
    {
        $label = ReservationStatus::EnAttente->label();

        $this->assertIsString($label);
        $this->assertNotEmpty($label);
    }

    /** @test */
    public function label_returns_translated_string_for_accepte()
    {
        $label = ReservationStatus::Accepte->label();

        $this->assertIsString($label);
        $this->assertNotEmpty($label);
    }

    /** @test */
    public function label_returns_translated_string_for_refuse()
    {
        $label = ReservationStatus::Refuse->label();

        $this->assertIsString($label);
        $this->assertNotEmpty($label);
    }

    /** @test */
    public function color_returns_warning_for_en_attente()
    {
        $this->assertEquals('warning', ReservationStatus::EnAttente->color());
    }

    /** @test */
    public function color_returns_success_for_accepte()
    {
        $this->assertEquals('success', ReservationStatus::Accepte->color());
    }

    /** @test */
    public function color_returns_danger_for_refuse()
    {
        $this->assertEquals('danger', ReservationStatus::Refuse->color());
    }

    /** @test */
    public function options_returns_array_with_all_cases()
    {
        $options = ReservationStatus::options();

        $this->assertIsArray($options);
        $this->assertCount(3, $options);
        $this->assertArrayHasKey('en_attente', $options);
        $this->assertArrayHasKey('accepte', $options);
        $this->assertArrayHasKey('refuse', $options);
    }

    /** @test */
    public function options_values_are_labels()
    {
        $options = ReservationStatus::options();

        $this->assertEquals(ReservationStatus::EnAttente->label(), $options['en_attente']);
        $this->assertEquals(ReservationStatus::Accepte->label(), $options['accepte']);
        $this->assertEquals(ReservationStatus::Refuse->label(), $options['refuse']);
    }
}
