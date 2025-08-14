<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SponsorshipBundleAppliedUser extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $bundle;

    public function __construct($user, $bundle)
    {
        $this->user = $user;
        $this->bundle = $bundle;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Thanks for Applying — Sponsorship Bundle'
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.sponsorship.applied-user',
            with: [
                'user'   => $this->user,
                'bundle' => $this->bundle,
            ]
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
