<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendEmail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($subject, $data)
    {
        $this->subject = $subject;
        $this->content = $data;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $asset = asset('/');

        // if (!empty($this->pdf)) {
        //     // return $this->subject($this->subject)->view('emails.template', ['content' => $this->content, 'asset_url' => $asset])->attach($this->pdf, ['as' => 'attachment.pdf', 'mime' => 'application/pdf']);
        //     return $this->subject($this->subject)->view('emails.template', ['content' => $this->content, 'asset_url' => $asset])->attachData($this->pdf->output(), 'attachment.pdf');
        // } else {
        return $this->subject($this->subject)->view('emails.template', ['content' => $this->content, 'asset_url' => $asset]);
        // }
    }
}
