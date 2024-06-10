<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Attachment;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class JobAppliedForCompany extends Mailable
{
    use Queueable, SerializesModels;

    public $jobItem;

    public $user;

    /**
     * Create a new message instance.
     */
    public function __construct($jobItem, $user)
    {
        $this->jobItem = $jobItem;
        $this->user = $user;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New Job Application Received For '.$this->jobItem->title,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.job-applied-for-company',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        // check if the user has a cv
        if ($this->user->cv) {
            return [

                Attachment::fromStorageDisk('public', $this->user->cv)
                    ->as('cv.pdf')
                    ->withMime('application/pdf'),
            ];
        }

        return [];
    }
}
