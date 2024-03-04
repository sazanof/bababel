<?php

namespace App\Mail;

use App\Helpers\NotificationHelper;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Attachment;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\SentMessage;
use Illuminate\Queue\SerializesModels;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\FileBag;

class FeedbackMail extends Mailable
{
    use Queueable, SerializesModels;

    public $subject;

    public string $title;

    public string $content;

    public null|array|FileBag $files;

    public ?array $emails;

    public User $user;

    /**
     * Create a new message instance.
     */
    public function __construct(User $user, string $title, string $message, null|array|FileBag $files = null)
    {
        $this->title = $title;
        $this->content = $message;
        $this->files = $files === null ? [] : $files;
        $this->user = $user;
        $this->emails = NotificationHelper::getSystemNotificationEmails();
        if (!empty($this->files)) {
            /** @var UploadedFile $file */
            foreach ($this->files as $file) {
                $this->attachData($file->getContent(), $file->getClientOriginalName());
            }
        }
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            to: $this->emails,
            subject: __('mail.feedback.subject')
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'mail.feedback',
        );
    }
}
