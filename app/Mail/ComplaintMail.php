<?php

namespace App\Mail;

use App\Models\Complaint;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Attachment;

class ComplaintMail extends Mailable
{
    use Queueable, SerializesModels;

    public $complaint;

    /**
     * Create a new message instance.
     */
    public function __construct(Complaint $complaint)
    {
        $this->complaint = $complaint;
    }

    /**
     * Email subject
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Inventory Complaint from ' . $this->complaint->user->role
        );
    }

    /**
     * Email content
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.complaint',
            with: [
                'complaint' => $this->complaint,
            ]
        );
    }

    /**
     * Attach image if exists
     */
    public function attachments(): array
    {
        if ($this->complaint->image) {
            return [
                Attachment::fromPath(
                    storage_path('app/public/' . $this->complaint->image)
                )
            ];
        }

        return [];
    }
}