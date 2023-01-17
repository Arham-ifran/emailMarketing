<?php

namespace App\Jobs;

use App\Models\Admin\Admin;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\CampaignExclude;
use App\Models\EmailCampaign;
use App\Models\EmailSendingLog;
use Carbon\Carbon;
use Webklex\IMAP\Facades\Client;
use Hashids;

class BounceJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
    }
    protected $folder_array = [];
    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        $this->getBouncedEmails();
    }

    public function getBouncedEmails()
    {
        // \Log::info('Checking Bounce emails with BounceJob');
        $client = Client::account('default');

        try {
            $client->connect();
        } catch (\Throwable $th) {
            \Log::info('Issue connecting to IMAP', array(
                'error' => $th
            ));
            // $admin = Admin::where('status', 1)->first();
            // $admin_email = $admin->email;
            $admin_email = settingValue('contact_email');
            SendMail::dispatch($admin_email, 'Bounce Email Account Issue', "There was a problem connecting to bounce email recieving account. Please check and update the bounce email credentials in IMAP settings.");
        }
        //Connect to the IMAP Server
        $host = "imap.gmail.com";

        if ($client->isConnected()) {

            // \Log::info('Connected to IMAP');
            if ($host == "outlook.office365.com") {
                $listTree = $client->getFolders(false);
            } else {
                $listTree = $client->getFolders();
                $secret = $client->getFolder('CheckedBouncedMails');
                if ($secret == Null)
                    $client->createFolder("CheckedBouncedMails");
            }

            foreach ($listTree as $oFolder) {
                $this->read_folder_recursive($oFolder);
            }

            $subjects = [];

            // \Log::info("bounce job folders:" , array(
            //     'folders' => $this->folder_array,
            // ));

            foreach ($this->folder_array as $folders) {
                if ($folders['folder_name'] != "CheckedBouncedMails") {
                    $folder_client = $client->getFolder($folders['folder_full_name']);
                    // $folder_messages = $folder_client->messages()->all()->get();
                    $folder_messages = $folder_client->search()->unseen()->get();
                    // \Log::info("bounce job folder msgs:" , array(
                    //     'folder' => $folders['folder_full_name'],
                    //     'messages' => $folder_messages
                    // ));
                    if (count($folder_messages)) {
                        foreach ($folder_messages as $messages) {
                            $body = $messages->getRawBody();

                            // \Log::info("message raw body in bounce job:" , array(
                            //     'messagebody' => $body,
                            // ));
                            try {
                                $campaign_id = $contact_id = $history_id = NULL;
                                preg_match_all('#campaign-id: (.*)#m', $body, $matches);
                                if (isset($matches[1])) {
                                    $campaignarr = $matches[1];
                                    if (isset($campaignarr[0]))
                                        $campaign_id = rtrim($campaignarr[0], "\r");
                                }
                                preg_match_all('#recipient-id: (.*)#m', $body, $matches);
                                if (isset($matches[1])) {
                                    $contactarr = $matches[1];
                                    if (isset($contactarr[0]))
                                        $contact_id = rtrim($contactarr[0], "\r");
                                }
                                preg_match_all('#reference-id: (.*)#m', $body, $matches);
                                if (isset($matches[1])) {
                                    $historyarr = $matches[1];
                                    if (isset($historyarr[0]))
                                        $history_id = rtrim($historyarr[0], "\r");
                                }
                                // \Log::info("header data in bounce job:" , array(
                                //     'campaign' => $campaign_id,
                                //     'contact' => $contact_id,
                                //     'history' => $history_id
                                // ));
                                // dd($campaign_id, Hashids::decode($contact_id)[0]);
                                if ($campaign_id && $contact_id && $history_id) {
                                    // if ($campaign_id && $contact_id) {
                                    //mark the contact as bounced
                                    $campaign_id = Hashids::decode($campaign_id)[0];
                                    $contact_id = Hashids::decode($contact_id)[0];
                                    $history_id = Hashids::decode($history_id)[0];
                                    $log = EmailSendingLog::where(['campaign_id' => $campaign_id, 'contact_id' => $contact_id, 'history_id' => $history_id])->first();
                                    // $log = EmailSendingLog::where(['campaign_id' => $campaign_id, 'contact_id' => $contact_id])->first();
                                    if ($log && $log->sent_at != NULL) {
                                        $log->bounced_at = $log->sent_at;
                                        $log->sent_at = NULL;
                                        $log->save();
                                    }
                                    // make it so that this campaing does not send to this contact in case of recursive campaign.
                                    $campaign = EmailCampaign::where('id', $campaign_id)->first();
                                    if ($campaign && $campaign->campaign_type == 3) {
                                        $data = ['contact_id' => $contact_id, 'campaign_id' => $campaign_id, 'user_id' => $campaign->user_id, 'type' => 2];
                                        $find = CampaignExclude::where('deleted_at', null)->where('type', 2)->where('campaign_id', $campaign_id)->where('contact_id', $contact_id)->first();
                                        if (!$find)
                                            CampaignExclude::create($data);
                                    }
                                }

                                $email_subject = $messages->getSubject();
                                print_r($email_subject);
                                array_push($subjects, $email_subject);
                                // $email_content = $messages->getTextBody();
                                // $html_content_ = $messages->getHTMLBody();

                                // marking the message as read.
                                //Move the current Message to 'INBOX.read'
                                $messages->setFlag('SEEN');
                                try {
                                    $messages->move('CheckedBouncedMails');
                                } catch (\Throwable $th) {
                                    //throw $th;
                                }
                                $client->expunge();
                                // else {
                                //     echo 'Message could not be moved';
                                // }

                            } catch (\Throwable $th) {
                                //throw $th;
                            }
                        }
                    }
                }
            }

            // adding next bouncejob to jobs table
            $next = Carbon::parse(now()->addMinutes(90));
            BounceJob::dispatch()->delay($next);
        }
    }

    public function read_folder_recursive($a, $parent = "0")
    {
        $detail['folder_name'] = $a->name;
        $detail['folder_full_name'] = $a->full_name;
        array_push($this->folder_array, $detail);

        if (!empty($a->children)) {
            foreach ($a->children as $child) {
                $this->read_folder_recursive($child, $a->name);
            }
        }
    }
}
