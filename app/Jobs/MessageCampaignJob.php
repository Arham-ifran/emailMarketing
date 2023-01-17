<?php

namespace App\Jobs;

use App\CustomClasses\TranslationHandler;
use App\Models\Admin\EmailTemplate;
use App\Models\Admin\PackageSubscription;
use App\Models\CampaignContact;
use App\Models\Contact;
use App\Models\CampaignExclude;
use App\Models\Notification;
use App\Models\CampaignHistory;
use App\Models\Group;
use App\Models\SmsCampaign;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Twilio\Rest\Client;
use Log;
use App\Models\SmsCampaignLogs;
use App\Models\User_log;
use Illuminate\Support\Facades\Auth;
use App\Services\PayUService\Exception;
use Carbon\Carbon;
use DateTime;
use DateTimeZone;
use Illuminate\Support\Facades\DB;
use Hashids;

class MessageCampaignJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $from_name, $from_number, $to_name, $to_number, $message, $twilioClient, $user_id, $contact_id, $history;

    protected $auth;
    protected $request;
    protected $campaign_id;
    protected $job_code;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($auth, $request, $campaign_id, $job_code)
    {
        $this->auth = $auth;
        $this->request = $request;
        $this->campaign_id = $campaign_id;
        $this->job_code = $job_code;
        $sid = settingValue('twilio_sid');
        $token = settingValue('twilio_auth_token');
        $this->twilioClient = new Client($sid, $token);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $user = $this->auth;
        $campaign_id = $this->campaign_id;
        $request = $this->request;

        $campaign = SmsCampaign::where(['user_id' => $user->id, 'id' => $campaign_id, 'job_code' => $this->job_code])->first();

        if (isset($campaign->id)) {

            print_r("here");
            //campaign initiated at
            $campaignHistory = CampaignHistory::create([
                'user_id' => $user->id,
                'campaign_id' => $campaign_id,
                'type' => 1,
            ]);

            //send campaign immediately and schedule once and recursively
            if ($campaign->type) {

                $campaign->update([
                    'status' => 7
                ]);
                // Notifying user of sending
                Notification::create([
                    'user_id' => $campaign->user_id,
                    'item_id' => $campaign->id,
                    'module' => 3,
                    'notification_type' => 3,
                    'notification_text' => "campaign_sending_in_progress '" . $campaign->name . "'",
                    'redirect_to' => "/sms-campaign/" . \Hashids::encode($campaign->id) . '/report',
                ]);
                User_log::create([
                    'user_id' => $campaign->user_id,
                    'item_id' => $campaign->id,
                    'module' => 3,
                    'log_type' => 3,
                ]);

                // $group = Group::where('id', $campaign->group_id)->first();
                $contacts = [];

                if ($campaign->group_ids && count(json_decode($campaign->group_ids)) != 0) {
                    $groups = Group::whereIn('id', array_column(json_decode($campaign->group_ids), 'id'))->with('contacts')->get();
                    foreach ($groups as $group) {
                        // $contacts = array_merge($contacts, $group->contacts->pluck('id')->toArray());
                        $col = array_search($group->id, array_column(json_decode($campaign->group_ids), 'id'));
                        $pivotid = array_column(json_decode($campaign->group_ids), 'last')[$col];
                        $grp_contacts = $group->contacts()->withPivot('id')->wherePivot('id', '<=', $pivotid)->get()->toArray();
                        $contacts = array_merge($contacts, array_column($grp_contacts, 'id'));
                    }
                }
                // $includes = CampaignContact::where('type', 1)->where('campaign_id', $campaign->id)->pluck('contact_id')->toArray();
                $includes = CampaignContact::where('type', 1)->where('campaign_id', $campaign->id)->where('id', '<=', $campaign->group_id)->pluck('contact_id')->toArray();
                $allcontacts = array_unique(array_merge($contacts, $includes));
                $contacts = Contact::whereIn('id', $allcontacts)->get()->toArray();
                // $contacts = array_map("unserialize", array_unique(array_map("serialize", $contacts)));

                // $sms_span1_start = PackageSettingsValue(4, 'start_range');
                // $sms_span1_end = PackageSettingsValue(4, 'end_range');
                // $sms_span1_price = PackageSettingsValue(4, 'price_without_vat');
                // $sms_span2_start = PackageSettingsValue(5, 'start_range');
                // $sms_span2_end = PackageSettingsValue(5, 'end_range');
                // $sms_span2_price = PackageSettingsValue(5, 'price_without_vat');
                // $sms_span3_start = PackageSettingsValue(6, 'start_range');
                // $sms_span3_price = PackageSettingsValue(6, 'price_without_vat');

                foreach ($contacts as $contact) {
                    $contact = Contact::where('id', $contact['id'])->first();
                    if ($contact->for_sms == 1 && $contact->subscribed) {
                        $excludes = CampaignExclude::where('type', 1)->where('campaign_id', $campaign->id)->pluck('contact_id')->toArray();
                        if ($excludes == NULL || (is_array($excludes) && in_array($contact->id, $excludes) == false)) {
                            // if (in_array($contact->id, $excludes) == false) {
                            // SendMail::dispatch($contact->email, 'SPlit Testing Campaign Subject ', "SPlit testing and " . "<img src=" . url('track-campaign') . "?_id=" . Hashids::encode($campaign_id) . "&referal_id=" . Hashids::encode($contact->id). "&history_id=" . Hashids::encode($campaignHistory->id) . " style='width:1px;height:1px;display:none;'>");

                            // Replacing dynamic attributes
                            $search = array("{{name}}");
                            $replace = array($contact->first_name . " " . $contact->last_name);
                            $changed_message = str_replace($search, $replace, $campaign->message);

                            $body = sprintf($changed_message, $contact->first_name . " " . $contact->last_name);
                            $time = now();
                            try {
                                $message = $this->twilioClient->messages->create(
                                    $contact->number,
                                    [
                                        'from' => settingValue('twilio_number'),
                                        'body' => $body
                                    ]
                                );
                                // save this to logs
                                $log = SmsCampaignLogs::create([
                                    'user_id' => $user->id,
                                    'contact_id' => $contact->id,
                                    'campaign_id' => $campaign->id,
                                    'to' => $message->to,
                                    'from' => $message->from,
                                    'body' => $message->body,
                                    'started_at' => $time,
                                    'sent_at' => now(),
                                    'sid' => $message->sid,
                                    'apiVersion' => $message->apiVersion,
                                    'history_id' => $campaignHistory->id,
                                ]);
                                // if ($user->package_subscription_id) {
                                //     $subscription = PackageSubscription::where('id', $user->package_subscription_id)->where('is_active', 1)->first();
                                //     if ($subscription) {
                                //         $subscription->update(['sms_used' => $subscription->sms_used + 1]);
                                //         if ($subscription->package_id == 9) {
                                //             $subscription->sms_paying_for += 1;
                                //             if ($subscription->sms_used >= $sms_span1_start && $subscription->sms_used <= $sms_span1_end) {
                                //                 $subscription->sms_to_pay += $sms_span1_price;
                                //             } else if ($subscription->sms_used >= $sms_span2_start && $subscription->sms_used <= $sms_span2_end) {
                                //                 $subscription->sms_to_pay += $sms_span2_price;
                                //             } else if ($subscription->sms_used >= $sms_span3_start) {
                                //                 $subscription->sms_to_pay += $sms_span3_price;
                                //             }
                                //             $subscription->save();
                                //         }
                                //     }
                                // }

                                if (isset($request['callback_url'])) {
                                    try {
                                        $callback_client = new Client();
                                        $callback_client->request('POST', $request['callback_url'], [
                                            'form_params' => [
                                                '_id' =>  Hashids::encode($campaign->id),
                                                'campaign_type' => "SMS Campaign",
                                                'status' => '1',
                                            ]
                                        ]);
                                    } catch (\Throwable $th) {
                                        //throw $th;
                                    }
                                }
                            } catch (\Throwable $e) {
                                \Log::info('twilio error: ', array(
                                    'error' => $e
                                ));
                                //throw $th;
                                // save this to logs
                                $log = SmsCampaignLogs::create([
                                    'user_id' => $user->id,
                                    'contact_id' => $contact->id,
                                    'campaign_id' => $campaign->id,
                                    'to' => $contact->number,
                                    'from' => settingValue('twilio_number'),
                                    'body' => $body,
                                    'started_at' => $time,
                                    'failed_at' => now(),
                                    'failed_reason' => "unknown",
                                    'history_id' => $campaignHistory->id,
                                ]);
                                if (isset($request['callback_url'])) {
                                    try {
                                        $callback_client = new Client();
                                        $callback_client->request('POST', $request['callback_url'], [
                                            'form_params' => [
                                                '_id' =>  Hashids::encode($campaign->id),
                                                'campaign_type' => "SMS Campaign",
                                                'status' => '0',
                                            ]
                                        ]);
                                    } catch (\Throwable $th) {
                                        //throw $th;
                                    }
                                }
                            }
                        }
                    }
                }
                //campaign completed at
                $campaignHistory->update([
                    'updated_at' => now(),
                ]);
                $campaign->update([
                    'status' => 2,
                ]);

                // notify user of completion
                Notification::create([
                    'user_id' => $campaign->user_id,
                    'item_id' => $campaign->id,
                    'module' => 4,
                    'notification_type' => 3,
                    'notification_text' => "campaign_sent '" . $campaign->name . "'",
                    'redirect_to' => "/sms-campaign/" . \Hashids::encode($campaign->id) . "/report",
                ]);
                User_log::create([
                    'user_id' => $campaign->user_id,
                    'item_id' => $campaign->id,
                    'module' => 4,
                    'log_type' => 3,
                ]);

                $email_template = EmailTemplate::where('type', 'campaign_sent_notification')->first();
                $email_template = transformEmailTemplateModel($email_template, $user->language);
                $name = $user->name;
                $email = $user->email;
                $subject = $email_template['subject'];
                $content = $email_template['content'];

                // create time
                $date = new DateTime($campaign->created_at, new DateTimeZone('UTC'));
                $date->setTimezone(new DateTimeZone($user->timezone));
                $created_at = $date->format('Y-m-d H:i:s');
                // update time
                $date = new DateTime($campaign->updated_at, new DateTimeZone('UTC'));
                $date->setTimezone(new DateTimeZone($user->timezone));
                $updated_at = $date->format('Y-m-d H:i:s');
                // sent time
                $date = new DateTime($campaignHistory->updated_at, new DateTimeZone('UTC'));
                $date->setTimezone(new DateTimeZone($user->timezone));
                $sent_at = $date->format('Y-m-d H:i:s');

                $search = array("{{name}}", "{{campaign_type}}", "{{campaign_name}}", "{{created_at}}", "{{updated_at}}", "{{sent_at}}", "{{app_name}}");
                $replace = array($name, TranslationHandler::getTranslation($user->language, 'SMS Campaigns'), $campaign->name, $created_at, $updated_at, $sent_at,  settingValue('site_title'));
                $content  = str_replace($search, $replace, $content);

                SendMail::dispatch($email, $subject, $content);

                // set next queue job if any recursive job remaining
                if ($campaign->type == 3) {

                    $campaign->update([
                        'no_of_time' => $campaign->no_of_time - 1
                    ]);

                    if ($campaign->no_of_time > 0) {
                        if ($request['recursive_campaign_type'] == 1) {
                            //weekly
                            $dayOfWeek = Carbon::parse('next ' . config('constants.days_of_week')[$request['day_of_week']]); //->toDateString();
                            $currentTime = Carbon::parse(now()->format('Y-m-d'));
                            $totalDuration = $currentTime->diff($dayOfWeek);
                            // if ($dayOfWeek->diff($currentTime) == 0) {
                            //     $today = Carbon::parse(now());
                            //     $totalDuration = $today->addWeek();
                            // }

                            MessageCampaignJob::dispatch($user, $request, $campaign->id, $this->job_code)->delay($totalDuration);
                        } elseif ($request['recursive_campaign_type'] == 2) {
                            //Monthly
                            $month = Carbon::now()->format('F');
                            $year = Carbon::now()->format('Y');
                            $day = $request['day_of_month'];

                            $selectedDay = Carbon::parse($month . ' ' . $day . ' ' . $year);
                            $selectedDay = $selectedDay->addMonth();

                            $selectedDay = Carbon::parse($selectedDay); //->toDateString();
                            $currentTime = Carbon::parse(now()->format('Y-m-d'));
                            $totalDuration = $currentTime->diff($selectedDay);


                            MessageCampaignJob::dispatch($user, $request, $campaign->id, $this->job_code)->delay($totalDuration);
                        } elseif ($request['recursive_campaign_type'] == 3) {
                            //Yearly
                            $month = config('constants.months_of_year')[$request['month_of_year']];
                            $day = $request['day_of_month'];
                            $year = date('Y');

                            $selectedDay = Carbon::parse($month . ' ' . $day . ' ' . $year)->addMonth(12);
                            $selectedDay = Carbon::parse($selectedDay); //->toDateString();
                            $currentTime = Carbon::parse(now()->format('Y-m-d'));
                            $totalDuration = $currentTime->diff($selectedDay);

                            MessageCampaignJob::dispatch($user, $request, $campaign->id, $this->job_code)->delay($totalDuration);
                        }
                    } else {
                        $campaign->update([
                            'status' => 3
                        ]);
                    }
                } else {
                    $campaign->update([
                        'status' => 3
                    ]);
                }
            }
        }
    }
}
