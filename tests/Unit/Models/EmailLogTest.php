<?php

namespace Tests\Unit\Models;

use App\Models\EmailLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmailLogTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_can_create_an_email_log(): void
    {
        $emailLog = EmailLog::create([
            'destinataire' => 'test@example.com',
            'sujet' => 'Test Subject',
            'body_preview' => 'This is a test email body',
            'sent_at' => now(),
        ]);

        $this->assertInstanceOf(EmailLog::class, $emailLog);
        $this->assertEquals('test@example.com', $emailLog->destinataire);
        $this->assertEquals('Test Subject', $emailLog->sujet);
    }

    public function test_it_has_all_fillable_attributes(): void
    {
        $emailLog = new EmailLog();
        $fillable = $emailLog->getFillable();

        $expectedFillable = [
            'destinataire',
            'cc',
            'bcc',
            'sujet',
            'body_preview',
            'sent_at',
        ];

        foreach ($expectedFillable as $attribute) {
            $this->assertContains($attribute, $fillable);
        }
    }

    public function test_it_can_store_cc_recipients(): void
    {
        $emailLog = EmailLog::create([
            'destinataire' => 'to@example.com',
            'cc' => 'cc1@example.com, cc2@example.com',
            'sujet' => 'Test with CC',
            'body_preview' => 'Test body',
            'sent_at' => now(),
        ]);

        $this->assertEquals('cc1@example.com, cc2@example.com', $emailLog->cc);
    }

    public function test_it_can_store_bcc_recipients(): void
    {
        $emailLog = EmailLog::create([
            'destinataire' => 'to@example.com',
            'bcc' => 'bcc1@example.com, bcc2@example.com',
            'sujet' => 'Test with BCC',
            'body_preview' => 'Test body',
            'sent_at' => now(),
        ]);

        $this->assertEquals('bcc1@example.com, bcc2@example.com', $emailLog->bcc);
    }

    public function test_it_casts_sent_at_to_datetime(): void
    {
        $now = now();
        $emailLog = EmailLog::create([
            'destinataire' => 'test@example.com',
            'sujet' => 'Test',
            'body_preview' => 'Body',
            'sent_at' => $now,
        ]);

        $this->assertInstanceOf(\Illuminate\Support\Carbon::class, $emailLog->sent_at);
        $this->assertEquals($now->format('Y-m-d H:i:s'), $emailLog->sent_at->format('Y-m-d H:i:s'));
    }

    public function test_it_can_store_long_body_preview(): void
    {
        $longBody = str_repeat('Lorem ipsum dolor sit amet. ', 50);
        
        $emailLog = EmailLog::create([
            'destinataire' => 'test@example.com',
            'sujet' => 'Long body test',
            'body_preview' => $longBody,
            'sent_at' => now(),
        ]);

        $this->assertEquals($longBody, $emailLog->body_preview);
    }

    public function test_it_can_create_email_log_with_null_optional_fields(): void
    {
        $emailLog = EmailLog::create([
            'destinataire' => 'test@example.com',
            'cc' => null,
            'bcc' => null,
            'sujet' => 'Test',
            'body_preview' => 'Body',
            'sent_at' => now(),
        ]);

        $this->assertNull($emailLog->cc);
        $this->assertNull($emailLog->bcc);
    }

    public function test_it_has_timestamps(): void
    {
        $emailLog = EmailLog::create([
            'destinataire' => 'test@example.com',
            'sujet' => 'Test',
            'body_preview' => 'Body',
            'sent_at' => now(),
        ]);

        $this->assertNotNull($emailLog->created_at);
        $this->assertNotNull($emailLog->updated_at);
    }

    public function test_it_can_update_email_log(): void
    {
        $emailLog = EmailLog::create([
            'destinataire' => 'original@example.com',
            'sujet' => 'Original Subject',
            'body_preview' => 'Original body',
            'sent_at' => now(),
        ]);

        $emailLog->update([
            'sujet' => 'Updated Subject',
        ]);

        $this->assertEquals('Updated Subject', $emailLog->fresh()->sujet);
    }

    public function test_it_can_delete_email_log(): void
    {
        $emailLog = EmailLog::create([
            'destinataire' => 'test@example.com',
            'sujet' => 'To be deleted',
            'body_preview' => 'Body',
            'sent_at' => now(),
        ]);

        $id = $emailLog->id;
        $emailLog->delete();

        $this->assertDatabaseMissing('email_logs', [
            'id' => $id,
        ]);
    }

    public function test_it_can_query_by_destinataire(): void
    {
        EmailLog::create([
            'destinataire' => 'user1@example.com',
            'sujet' => 'Email 1',
            'body_preview' => 'Body 1',
            'sent_at' => now(),
        ]);

        EmailLog::create([
            'destinataire' => 'user2@example.com',
            'sujet' => 'Email 2',
            'body_preview' => 'Body 2',
            'sent_at' => now(),
        ]);

        $logs = EmailLog::where('destinataire', 'user1@example.com')->get();

        $this->assertCount(1, $logs);
        $this->assertEquals('Email 1', $logs->first()->sujet);
    }

    public function test_it_can_query_by_date_range(): void
    {
        $yesterday = now()->subDay();
        $today = now();
        $tomorrow = now()->addDay();

        EmailLog::create([
            'destinataire' => 'test@example.com',
            'sujet' => 'Yesterday email',
            'body_preview' => 'Body',
            'sent_at' => $yesterday,
        ]);

        EmailLog::create([
            'destinataire' => 'test@example.com',
            'sujet' => 'Today email',
            'body_preview' => 'Body',
            'sent_at' => $today,
        ]);

        $logs = EmailLog::where('sent_at', '>=', $today->startOfDay())->get();

        $this->assertCount(1, $logs);
        $this->assertEquals('Today email', $logs->first()->sujet);
    }

    public function test_it_can_order_by_sent_at(): void
    {
        EmailLog::create([
            'destinataire' => 'test@example.com',
            'sujet' => 'Second',
            'body_preview' => 'Body',
            'sent_at' => now()->addMinutes(10),
        ]);

        EmailLog::create([
            'destinataire' => 'test@example.com',
            'sujet' => 'First',
            'body_preview' => 'Body',
            'sent_at' => now(),
        ]);

        $logs = EmailLog::orderBy('sent_at', 'asc')->get();

        $this->assertEquals('First', $logs->first()->sujet);
        $this->assertEquals('Second', $logs->last()->sujet);
    }
}
