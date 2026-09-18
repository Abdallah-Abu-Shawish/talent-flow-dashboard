<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CompanyInvitationWithdrawnMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $recipientName,
        public string $companyName,
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'TalentFlow AI - Invitation Withdrawn',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.company-invitation-withdrawn',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}