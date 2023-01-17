<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Hashids;
use Illuminate\Support\Facades\Crypt;

class SendCampaignEmail extends Mailable
{
    use Queueable, SerializesModels;
    // protected $pdf;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($headerData, $sender_data, $subject, $data)
    {
        $this->headerData = $headerData;
        $this->sender_data = $sender_data; //[sender_name, sender_email, reply_to_email]
        $this->subject = $subject;
        $this->content = $data;
        // $this->pdf = $pdf;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        if ($this->headerData != []) {
            $this->withSwiftMessage(function ($message) {
                $headers = $message->getHeaders();
                $headers->addTextHeader('Return-Path', 'no-reply@marketingemail.biz');
                // $headers->addTextHeader('campaign-Sender', $this->sender_data[0] . ' <' . $this->sender_data[1] . '>');
                $headers->addTextHeader('Sender', $this->sender_data[0] . ' <' . $this->sender_data[1] . '>');
                // $headers->addTextHeader('From', $this->sender_data[0] . ' <' . $this->sender_data[1] . '>');
                $headers->addTextHeader('x-campaign-id', Hashids::encode($this->headerData[0]));
                $headers->addTextHeader('x-recipient-id', Hashids::encode($this->headerData[1]));
                $headers->addTextHeader('x-reference-id', Hashids::encode($this->headerData[2]));
            });
        }
        $asset = asset('/');
        $encrypted = "";
        if ($this->headerData != [])
            $encrypted = Crypt::encryptString($this->headerData[1]);

        if ($this->headerData == []) {
            return $this
                // ->from('no-reply@marketingemail.biz', 'Sameera')
                // ->replyTo('saeed@arhamsoft.com', 'Saeed')
                ->subject($this->subject)
                ->view('emails.campaign', ['reply_to' => $this->sender_data[2], 'content' => $this->content, 'asset_url' => $asset, 'unsubscribe' => url('/')]);
        } else {
            if ($this->sender_data[2])
                return $this
                    ->subject($this->subject)
                    ->from('no-reply@marketingemail.biz', $this->sender_data[0])
                    ->replyTo(trim($this->sender_data[2]), $this->sender_data[0])
                    ->view('emails.campaign', ['reply_to' => trim($this->sender_data[2]), 'content' => $this->content, 'asset_url' => $asset, 'unsubscribe' => url('/') . "/unsubscribe?code=" . $encrypted]);
            else
                return $this
                    ->subject($this->subject)
                    ->from('no-reply@marketingemail.biz', $this->sender_data[0])
                    ->view('emails.campaign', ['reply_to' => '',  'content' => $this->content, 'asset_url' => $asset, 'unsubscribe' => url('/') . "/unsubscribe?code=" . $encrypted]);
        }
    }
}
