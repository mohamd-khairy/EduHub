<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Attachment;

class SendCvMail extends Mailable
{
    use Queueable, SerializesModels;

    public $name;
    public $cvPath;

    public function __construct($name, $cvPath)
    {
        $this->name = $name;
        $this->cvPath = $cvPath;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Application for Senior Software Engineer Position - laravel',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.cv',
            with: [
                'name' => $this->name,
            ],
        );
    }

    public function attachments(): array
    {
        return [
            Attachment::fromPath($this->cvPath)
                ->as('My_CV.pdf')
                ->withMime('application/pdf'),
        ];
    }
}
