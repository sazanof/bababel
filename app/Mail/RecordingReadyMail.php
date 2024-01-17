<?php

namespace App\Mail;

use App\Models\Meeting;
use App\Models\Recording;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RecordingReadyMail extends Mailable
{
    use Queueable, SerializesModels;

    public Recording $recording;
    public Meeting $meeting;
    public string $msg;

    /**
     * Create a new message instance.
     */
    public function __construct(Recording $recording)
    {
        $this->recording = $recording;
        $recording->load('meeting');
        $this->meeting = $recording->meeting;
        $this->msg = __('mail.recording.ready_message', ['name' => $this->meeting->name]);
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('mail.recording.ready', ['name' => $this->meeting->name]),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'mail.recording-ready',
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
