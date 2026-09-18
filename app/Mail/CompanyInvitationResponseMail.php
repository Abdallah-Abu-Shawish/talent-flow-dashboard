<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CompanyInvitationResponseMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $managerName,
        public string $employeeName,
        public string $companyName,
        public string $status,
    ) {
    }

    public function envelope(): Envelope
    {
        $subject =
            $this->status === 'accepted'
                ? 'TalentFlow AI - Invitation Accepted'
                : 'TalentFlow AI - Invitation Declined';

        return new Envelope(
            subject: $subject,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.company-invitation-response',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}