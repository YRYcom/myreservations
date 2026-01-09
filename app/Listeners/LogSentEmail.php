<?php

namespace App\Listeners;

use App\Models\EmailLog;
use Illuminate\Mail\Events\MessageSent;
use Illuminate\Support\Facades\Log;

class LogSentEmail
{
    /**
     * Handle the event.
     */
    public function handle(MessageSent $event): void
    {
        try {
            $message = $event->sent->getOriginalMessage();
            
            // Extract recipients
            $to = $message->getTo();
            $destinataire = null;
            $all_recipients = [];
            
            if (!empty($to)) {
                foreach ($to as $address) {
                    $email = $address->getAddress();
                    $all_recipients[] = $email;
                    if ($destinataire === null) {
                        $destinataire = $email;
                    }
                }
            }
            
            // Extract CC
            $cc = $message->getCc();
            $cc_recipients = [];
            if (!empty($cc)) {
                foreach ($cc as $address) {
                    $cc_recipients[] = $address->getAddress();
                }
            }
            
            // Extract BCC
            $bcc = $message->getBcc();
            $bcc_recipients = [];
            if (!empty($bcc)) {
                foreach ($bcc as $address) {
                    $bcc_recipients[] = $address->getAddress();
                }
            }
            
            // Extract subject
            $sujet = $message->getSubject() ?? 'Sans sujet';
            
            // Extract body preview (first 500 chars)
            $body = $message->getHtmlBody() ?? $message->getTextBody() ?? '';
            $body_preview = mb_substr(strip_tags($body), 0, 500);
            
            // Save to database
            if ($destinataire) {
                EmailLog::create([
                    'destinataire' => $destinataire,
                    'cc' => !empty($cc_recipients) ? implode(', ', $cc_recipients) : null,
                    'bcc' => !empty($bcc_recipients) ? implode(', ', $bcc_recipients) : null,
                    'sujet' => $sujet,
                    'body_preview' => $body_preview,
                    'sent_at' => now(),
                ]);
            }
        } catch (\Throwable $e) {
            // Log error but don't fail - email sending should never be blocked
            Log::error('Email logging failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }
}
