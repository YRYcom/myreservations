<?php

namespace App\Listeners;

use App\Enums\ActivityAction;
use App\Models\ActivityLog;
use Illuminate\Mail\Events\MessageSent;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class LogSentEmail
{
    /**
     * Determine action from email subject.
     */
    private function getActionFromSubject(string $subject): ?ActivityAction
    {
        // Map subject translations to actions
        $subjectMap = [
            __('filament.emails.reservation_approved.subject') => ActivityAction::EMAIL_RESERVATION_APPROVED,
            __('filament.emails.reservation_rejected.subject') => ActivityAction::EMAIL_RESERVATION_REJECTED,
            __('filament.emails.reservation_reminder.subject') => ActivityAction::EMAIL_RESERVATION_REMINDER,
            __('filament.emails.reservation_pending_manager.subject') => ActivityAction::EMAIL_RESERVATION_PENDING_MANAGER,
            __('filament.emails.reservation_pending_user.subject') => ActivityAction::EMAIL_RESERVATION_PENDING_USER,
            __('filament.emails.reservation_pending_self_manager.subject') => ActivityAction::EMAIL_RESERVATION_PENDING_SELF_MANAGER,
        ];
        
        return $subjectMap[$subject] ?? null;
    }

    /**
     * Handle the event.
     */
    public function handle(MessageSent $event): void
    {
        try {
            $message = $event->sent->getOriginalMessage();
            
            // Extract subject
            $sujet = $message->getSubject() ?? 'Sans sujet';
            
            // Get the action from the subject
            $action = $this->getActionFromSubject($sujet);
            
            // If we can't determine the action, skip logging
            if (!$action) {
                Log::info('Activity log skipped - unknown email subject', ['subject' => $sujet]);
                return;
            }
            
            // Extract class_name and reference_id from custom headers
            $headers = $message->getHeaders();
            $class_name = $headers->has('X-Activity-Class') 
                ? $headers->get('X-Activity-Class')->getBodyAsString() 
                : null;
            $reference_id = $headers->has('X-Activity-Reference-Id') 
                ? (int) $headers->get('X-Activity-Reference-Id')->getBodyAsString() 
                : null;
            
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
            
            // Extract body preview (first 500 chars)
            $body = $message->getHtmlBody() ?? $message->getTextBody() ?? '';
            $body_preview = mb_substr(strip_tags($body), 0, 500);
            
            // Prepare value array with all important email details
            $value = [
                'destinataire' => $destinataire,
                'all_recipients' => $all_recipients,
                'cc' => $cc_recipients,
                'bcc' => $bcc_recipients,
                'sujet' => $sujet,
                'body_preview' => $body_preview,
            ];
            
            // Get current authenticated user (will be null for cron jobs)
            $user_id = Auth::id();
            
            // Save to database
            if ($destinataire) {
                ActivityLog::create([
                    'action' => $action,
                    'class_name' => $class_name,
                    'reference_id' => $reference_id,
                    'value' => $value,
                    'user_id' => $user_id,
                ]);
                
                Log::info('Activity logged successfully', [
                    'action' => $action->value,
                    'class_name' => $class_name,
                    'reference_id' => $reference_id,
                    'destinataire' => $destinataire,
                    'user_id' => $user_id,
                ]);
            }
        } catch (\Throwable $e) {
            // Log error but don't fail - email sending should never be blocked
            Log::error('Activity logging failed for email', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }
}
