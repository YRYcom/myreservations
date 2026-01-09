<?php

namespace App\Listeners;

use App\Models\EmailLog;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Events\MessageSent;
use Illuminate\Queue\InteractsWithQueue;

class LogSentEmail
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(MessageSent $event): void
    {
        try {
            $message = $event->sent->getOriginalMessage();
            
            $to = $message->getTo();
            $destinataire = null;
            
            if (!empty($to)) {
                $firstRecipient = is_array($to) ? reset($to) : $to;
                $destinataire = $firstRecipient->getAddress();
            }
            
            $sujet = $message->getSubject() ?? 'Sans sujet';
            
            if ($destinataire) {
                EmailLog::create([
                    'destinataire' => $destinataire,
                    'sujet' => $sujet,
                    'sent_at' => now(),
                ]);
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Erreur lors du logging d\'email: ' . $e->getMessage());
        }
    }
}
