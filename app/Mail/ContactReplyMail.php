<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContactReplyMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $replyMessage;
    public string $originalSubject;

    /**
     * Create a new message instance.
     */
    public function __construct(string $replyMessage, string $originalSubject = '')
    {
        $this->replyMessage = $replyMessage;
        $this->originalSubject = $originalSubject;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $sub = $this->originalSubject ? 'Re: ' . $this->originalSubject : 'Reply to your inquiry';
        return new Envelope(
            subject: $sub,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.contact-reply',
        );
    }
}
