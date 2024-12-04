<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TaskCreatedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $subject;
    public $task;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($subject, $task)
    {

        $this->subject = $subject;
        $this->task = $task;
    }

    public function build()
    {
        return $this->subject($this->subject)
            ->html($this->task);
    }
}
