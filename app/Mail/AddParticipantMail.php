<?php

namespace App\Mail;

use App\Models\Meeting;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AddParticipantMail extends Mailable
{
    use Queueable, SerializesModels;

    public $subject;
    public string $msg;
    public User $user;
    public Meeting $meeting;

    /**
     * Create a new message instance.
     */
    public function __construct(User $user, Meeting $meeting)
    {
        $this->user = $user;
        $this->meeting = $meeting;
        $this->subject = __('mail.participant.create');
        $this->msg = __('mail.participant.create_message', ['org' => $meeting->owner->lastname . ' ' . $meeting->owner->firstname]);
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'mail.participant-add',
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
