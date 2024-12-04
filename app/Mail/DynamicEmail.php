<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DynamicEmail extends Mailable
{
    use Queueable, SerializesModels;


    public $attachmentPath;
    public $subject;
    public $html;

    public function __construct($subject = '', $html = '', $attachmentPath = '')
    {
        $this->subject = $subject;
        $this->html = $html;
        $this->attachmentPath = $attachmentPath;
    }

    public function build()
    {
        if ($this->attachmentPath != ''){
            return $this->subject($this->subject)
                ->html($this->html)
                ->attach($this->attachmentPath);
        }else{
            return $this->subject($this->subject)
                ->html($this->html);
        }
    }

}
