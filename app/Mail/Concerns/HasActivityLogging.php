<?php

namespace App\Mail\Concerns;

use Symfony\Component\Mime\Email;

trait HasActivityLogging
{
    /**
     * Add activity logging metadata to the email headers.
     */
    protected function addActivityMetadata(Email $message): void
    {
        $headers = $message->getHeaders();
        
        // Add class name
        $headers->addTextHeader('X-Activity-Class', static::class);
        
        // Add reference ID if reservation exists
        if (isset($this->reservation) && $this->reservation) {
            $headers->addTextHeader('X-Activity-Reference-Id', (string) $this->reservation->id);
        }
    }
}
