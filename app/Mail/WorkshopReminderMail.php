<?php

namespace App\Mail;

use App\Models\User;
use App\Models\Workshop;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WorkshopReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly User $user,
        public readonly Workshop $workshop,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Reminder: {$this->workshop->title} è domani",
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.workshop-reminder',
        );
    }

    /**
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
