<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

final class CompanyRequestStatusMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    public function __construct(
        public string $status,
        public string $companyName,
        public ?string $reviewNote = null,
    ) {}

    public function envelope(): Envelope
    {
        $subject = match ($this->status) {
            'approved' =>
                'Your company request has been approved',

            'rejected' =>
                'Update on your company request',

            default =>
                'Company request update',
        };

        return new Envelope(
            subject: $subject,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.company-request-status',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}