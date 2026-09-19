<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CompanyMemberLeftMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $managerName,
        public string $companyName,
        public string $memberName,
        public string $memberEmail,
        public string $memberRole,
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Employee left your company workspace',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.company-member-left',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}