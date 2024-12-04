<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $inquiry;
    public $type;
    public $other;
    public $title;

    /**
     * Create a new job instance.
     *
     * @param $title
     * @param $type
     * @param $inquiry
     * @param $other
     */
    public function __construct(string $title, string $type, object $inquiry, array $other = [])
    {

        Log::info($inquiry.' '.$title.' '.$type.' '.json_encode($other));
        $this->title = $title;
        $this->inquiry = $inquiry;
        $this->type = $type;
        $this->other = $other;
    }
    public function handle()
    {
        try {
            $this->inquiry ? Log::info("User Id: ".json_encode($this->inquiry->id)) : Log::info("User Id Unable to access");
            sendDynamicEmail($this->title, $this->type, [$this->inquiry->id], $this->other);
            Log::info('Email sent successfully');
        }catch (\Exception $error){
            Log::error('Error accessing inquiry ID: ' . $error->getMessage());
            Log::error($error->getTraceAsString());
        }
    }
}
