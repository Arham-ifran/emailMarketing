<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Mail\SendEmail;
use Mail;

class SendMail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $email, $subject, $content, $pdf, $lang, $filename;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($email, $subject, $content, $pdf = "", $filename = '', $lang = 'en')
    {
        $this->email = $email;
        $this->subject = $subject;
        $this->content = $content;
        $this->pdf = $pdf;
        $this->filename = $filename;
        $this->lang = $lang;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $detail = new SendEmail($this->subject, $this->content, $this->pdf);

        // $lang_file = public_path('i18n/translations/' . $lang . '.json');
        // $lang_arr = json_decode(file_get_contents($lang_file), true);

        // if (!empty($this->pdf)) {
        //     $subject = $this->subject;
        //     $email = $this->email;
        //     $pdf = $this->pdf;
        //     try {
        //         Mail::send('mails.mail', ['content' => $this->content], function ($message) use ($email, $subject, $pdf) {
        //             $message->to($email)
        //                 ->subject($subject)
        //                 ->attachData($pdf->output(), $this->filename);
        //         });
        //     } catch (\Exception $e) {
        //         \Log::info('Send Email Exception', array(
        //             'Message' => $e->getMessage()
        //         ));
        //     }
        // } else {
        Mail::to(trim($this->email))->send($detail);
        // }
    }
}
