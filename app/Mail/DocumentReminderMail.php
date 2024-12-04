<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DocumentReminderMail extends Mailable
{
    use Queueable, SerializesModels;


    /**
     * Create a new message instance.
     *
     * @param $inquiry
     * @param $documentList
     */
    public function __construct($inquiry, $documentList)
    {
        $this->inquiry = $inquiry;
        $this->documentList = $documentList;
    }

    public function build()
    {
        $user_name = $this->inquiry->first_name.' '.$this->inquiry->last_name;
        return $this->subject('Document Reminder')
            ->view('content.apps.email.document_reminder')
            ->with(['user' => $user_name, 'documentList' => $this->documentList]);

    }
}
