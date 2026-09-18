<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

final class CompanyMemberActionMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    public function __construct(
        public string $recipientName,
        public string $companyName,
        public string $action,
    ) {}

    public function envelope(): Envelope
    {
        $subject = match ($this->action) {
            'suspended' =>
                'TalentFlow AI - Company access temporarily disabled',

            'reactivated' =>
                'TalentFlow AI - Company access restored',

            'removed' =>
                'TalentFlow AI - Removed from company',

            default =>
                'TalentFlow AI - Company access update',
        };

        return new Envelope(
            subject: $subject,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.company-member-action',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
