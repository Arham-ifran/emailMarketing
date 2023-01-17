<?php

namespace App\Jobs;

use App\Mail\SendCampaignEmail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Mail;

class SendCapmaignMail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $headerData, $email, $subject, $content, $pdf, $sender_data;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($headerData, $sender_data, $email, $subject, $content)
    {
        $this->headerData = $headerData;
        $this->email = $email;
        $this->subject = $subject;
        $this->sender_data = $sender_data;
        $this->content = $content;
        // $this->pdf = $pdf;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // $detail = new SendCampaignEmail($this->headerData, $this->subject, $this->content, $this->pdf);
        $detail = new SendCampaignEmail($this->headerData, $this->sender_data, $this->subject, $this->content);

        // \Log::info('sending campaign mail', array(
        //     'to' => $this->email,
        //     'detail' => $detail
        // ));
        Mail::to(trim($this->email))->send($detail);
    }
}
