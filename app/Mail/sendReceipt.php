<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class sendReceipt extends Mailable
{
    use Queueable, SerializesModels;

    public $attachmentPath;
    public $inquiry;
    public $receiptData;

    public function __construct($inquiry, $receiptData, $attachmentPath)
    {
        $this->inquiry = $inquiry;
        $this->receiptData = $receiptData;
        $this->attachmentPath = $attachmentPath;
    }

    public function build()
    {
        return $this->subject('Document Reminder')
            ->view('content.apps.email.send_receipt')
            ->with(['inquiry' => $this->inquiry, 'receiptData' => $this->receiptData])
            ->attach($this->attachmentPath);
    }

}
