<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SiteOnlineMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $appName;
    public string $appUrl;
    public string $timestamp;

    /**
     * Create a new message instance.
     */
    public function __construct(string $appName, string $appUrl, string $timestamp)
    {
        $this->appName = $appName;
        $this->appUrl = $appUrl;
        $this->timestamp = $timestamp;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '✅ سایت آنلاین شد - ' . $this->appName,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.site-online',
            with: [
                'appName' => $this->appName,
                'appUrl' => $this->appUrl,
                'timestamp' => $this->timestamp,
            ],
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
