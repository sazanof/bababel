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
use Illuminate\Support\Facades\Auth;

class UpdateMeetingDateMail extends Mailable
{
    use Queueable, SerializesModels;

    public Meeting $meeting;
    public $subject;
    public string $msg;

    /**
     * Create a new message instance.
     */
    public function __construct(Meeting $meeting, User $user = null)
    {
        $user = is_null($user) ? Auth::user() : $user;
        $this->meeting = $meeting;
        $this->subject = __('mail.meeting.update_date');
        $this->msg = __('mail.meeting.update_date_message', ['name' => $user->firstname]);
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
            view: 'mail.meeting-update-date',
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
