<?php

namespace App\Mail;

use App\Mail\Concerns\HasActivityLogging;
use App\Models\Reservation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Symfony\Component\Mime\Email;

class ReservationReminderNotification extends Mailable
{
    use Queueable, SerializesModels, HasActivityLogging;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public Reservation $reservation
    ) {
        $this->withSymfonyMessage(function (Email $message) {
            $this->addActivityMetadata($message);
        });
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('filament.emails.reservation_reminder.subject'),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.reservation-reminder',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
