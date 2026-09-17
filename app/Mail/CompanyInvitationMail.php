<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CompanyInvitationMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $invitedUserName;
    public string $inviterName;
    public string $companyName;
    public string $invitationUrl;

    public function __construct(
        string $invitedUserName,
        string $inviterName,
        string $companyName,
        string $invitationUrl,
    ) {
        $this->invitedUserName = $invitedUserName;
        $this->inviterName = $inviterName;
        $this->companyName = $companyName;
        $this->invitationUrl = $invitationUrl;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'You have been invited to join ' . $this->companyName,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.company-invitation',
            with: [
                'invitedUserName' => $this->invitedUserName,
                'inviterName' => $this->inviterName,
                'companyName' => $this->companyName,
                'invitationUrl' => $this->invitationUrl,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}