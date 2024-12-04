<?php

namespace App\Http\Controllers;

use App\Mail\DynamicEmail;
use App\Models\EmailTemplate;
use App\Models\Inquiry;
use App\Models\Setting;
use App\Models\SMSTemplate;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Mail;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function gst(){
        return Setting::pluck('gst')->first();
    }
    public function tds(){
        return Setting::pluck('tds')->first();
    }

    public function sendDynamicEmail($type = '', $inquiry_ids = [],  $other = [] ){
        $documentList = '';
        if ($type == 'document_reminder' || $type == 'document_list'){
            $documentList = '<ul>';
            foreach ($other['document'] as $doc){
                $documentList .= '<li>'.$doc->name.'</li>';
            }
            $documentList = '</ul>';
        }
        $file = '';
        $amount_paid = '';
        if ($type == 'send_receipt'){
            $receiptData = $other['paymentReceipt'];
            $amount_paid = $receiptData->amount_paid;
            $file = storage_path('app/public/'.$receiptData->receipt_name);
        }
            $setting = Setting::pluck($type)->first();
        if ($setting){
            foreach ($inquiry_ids as $inquiry_id){
                $inquiry = Inquiry::where('id', $inquiry_id)->first();
                $emailTemplate = EmailTemplate::where('id', $setting)->first();
                $template = $emailTemplate->html;
                $template = str_replace('{first_name}', $inquiry->first_name, $template);
                $template = str_replace('{last_name}', $inquiry->last_name, $template);
                $template = str_replace('{full_name}', $inquiry->first_name.' '.$inquiry->last_name, $template);
                $template = str_replace('{email}', $inquiry->email, $template);
                $template = str_replace('{mobile_no}', $inquiry->mobile_one, $template);
                $template = str_replace('{inquiry_no}', $inquiry->inquiry_no, $template);
                $template = str_replace('{registration_no}', $inquiry->inquiry_no, $template);
                $template = str_replace('{document_list}', $documentList, $template);
                $template = str_replace('{amount_paid}', $amount_paid, $template);

                Mail::to($inquiry->email)->send(new DynamicEmail($emailTemplate->subject, $template, $file));
            }
        }
    }

    public function sendDynamicSMS($type = '', $inquiry_id = '',  $other = [] ){

        try {
            $amount_paid = '';
            if ($type == 'send_receipt'){
                $receiptData = $other['paymentReceipt'];
                $amount_paid = $receiptData->amount_paid;
            }
            $setting = Setting::pluck($type)->first();

            if ($setting){

                $inquiry = Inquiry::where('id', $inquiry_id)->first();
                $emailTemplate = SMSTemplate::where('id', $setting)->first();
                $template = $emailTemplate->message;
                $template = str_replace('{first_name}', $inquiry->first_name, $template);
                $template = str_replace('{last_name}', $inquiry->last_name, $template);
                $template = str_replace('{full_name}', $inquiry->first_name.' '.$inquiry->last_name, $template);
                $template = str_replace('{email}', $inquiry->email, $template);
                $template = str_replace('{mobile_no}', $inquiry->mobile_one, $template);
                $template = str_replace('{inquiry_no}', $inquiry->inquiry_no, $template);
                $template = str_replace('{registration_no}', $inquiry->inquiry_no, $template);
                $template = str_replace('{amount_paid}', $amount_paid, $template);

                sendSMS($template, $inquiry->mobile_one);
            }
        }catch (\Exception $error){
            dd($error->getMessage());
        }
    }
}
