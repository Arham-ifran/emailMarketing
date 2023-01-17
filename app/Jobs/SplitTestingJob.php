<?php

namespace App\Jobs;

use App\CustomClasses\TranslationHandler;
use App\Jobs\SendMail;
use App\Models\Admin\EmailTemplate;
use App\Models\Admin\PackageSubscription;
use App\Models\CampaignContact;
use App\Models\CampaignExclude;
use App\Models\CampaignHistory;
use App\Models\Contact;
use App\Models\EmailCampaign;
use App\Models\EmailCampaignTemplate;
use App\Models\Group;
use App\Models\EmailSendingLog;
use App\Models\Notification;
use App\Models\SplitTestSubject;
use App\Models\User_log;
use Carbon\Carbon;
use DateTime;
use DateTimeZone;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Hashids;
use GuzzleHttp\Client;
use DOMDocument;

class SplitTestingJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

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
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $user = $this->auth;
        $request = $this->request;
        $campaign_id = $this->campaign_id;

        $campaign = EmailCampaign::where(['user_id' => $user->id, 'id' => $campaign_id, 'is_split_testing' => 1, 'job_code' => $this->job_code])->first();

        if (isset($campaign->id)) {
            $campaign_type = $campaign->campaign_type == 1 ? "Immidiate" : ($campaign->campaign_type == 2 ? "Scheduled" : "Recursive");

            //campaign initiated at
            $campaignHistory = CampaignHistory::create([
                'user_id' => $user->id,
                'campaign_id' => $campaign_id,
                'type' => 2,
                'created_at' => date('Y-m-d H:i:s'),
            ]);

            //send campaign immediately and schedule once and recursively
            if ($campaign->campaign_type) {

                $campaign->update([
                    'status' => 4
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
                // $includes = CampaignContact::where('type', 2)->where('campaign_id', $campaign->id)->pluck('contact_id')->toArray();
                $includes = CampaignContact::where('type', 2)->where('campaign_id', $campaign->id)->where('id', '<=', $campaign->group_id)->pluck('contact_id')->toArray();
                $allcontacts = array_unique(array_merge($contacts, $includes));
                $contacts = Contact::whereIn('id', $allcontacts)->get()->toArray();

                if ($campaign->split_test_param == 1) {
                    $emailTemplate = EmailCampaignTemplate::where('id', $campaign->template_id)->first();
                }

                // Notifying user of sending
                Notification::create([
                    'user_id' => $campaign->user_id,
                    'item_id' => $campaign->id,
                    'module' => 3,
                    'notification_type' => 3,
                    'notification_text' => "campaign_sending_in_progress '" . $campaign->name . "'",
                    'redirect_to' => "/split-testing/" . \Hashids::encode($campaign->id) . "/report",
                ]);
                User_log::create([
                    'user_id' => $campaign->user_id,
                    'item_id' => $campaign->id,
                    'module' => 3,
                    'log_type' => 3,
                ]);

                $excluded = CampaignExclude::where('type', 2)->where('campaign_id', $campaign->id)->pluck('contact_id')->toArray();
                $total_contacts = count($contacts) - count($excluded);
                //(10 * 50) / 100;
                // $contactConversion = ($total_contacts * $campaign->size_of_group) / 100;
                $contactConversion = (int)(($campaign->size_of_group / 100) * $total_contacts);
                $subject_id = NULL;
                $content_id = NULL;
                $i = 0;

                // $email_span1_start = PackageSettingsValue(1, 'start_range');
                // $email_span1_end = PackageSettingsValue(1, 'end_range');
                // $email_span1_price = PackageSettingsValue(1, 'price_without_vat');
                // $email_span2_start = PackageSettingsValue(2, 'start_range');
                // $email_span2_end = PackageSettingsValue(2, 'end_range');
                // $email_span2_price = PackageSettingsValue(2, 'price_without_vat');
                // $email_span3_start = PackageSettingsValue(3, 'start_range');
                // $email_span3_price = PackageSettingsValue(3, 'price_without_vat');

                foreach ($contacts as $contact) {
                    $contact = Contact::where('id', $contact['id'])->first();
                    $subject = $campaign->subject;
                    if ($contact->for_email == 1 && $contact->subscribed) {
                        $excludes = CampaignExclude::where('type', 2)->where('campaign_id', $campaign->id)->pluck('contact_id')->toArray();
                        if ($excludes == NULL || (is_array($excludes) && in_array($contact->id, $excludes) == false)) {

                            if ($contactConversion >= ($i + 1)) {
                                if ($campaign->split_test_param == 1) {
                                    // $campaign->split_subject_line_2;
                                    $sub = SplitTestSubject::where('id', $campaign->split_subject_line_2)->first();
                                    $subject = $sub->split_subject;
                                    $subject_id = $sub->id;
                                } else {
                                    $emailTemplate = EmailCampaignTemplate::where('id', $campaign->split_email_content_2)->first();
                                    $content_id = $emailTemplate->id;
                                }
                            } else {
                                if ($campaign->split_test_param == 1) {
                                    // $campaign->split_subject_line_1;
                                    $sub = SplitTestSubject::where('id', $campaign->split_subject_line_1)->first();
                                    $subject = $sub->split_subject;
                                    $subject_id = $sub->id;
                                } else {
                                    $emailTemplate = EmailCampaignTemplate::where('id', $campaign->split_email_content_1)->first();
                                    $content_id = $emailTemplate->id;
                                }
                            }
                            $i++;

                            // Replacing dynamic attributes
                            $search = array("{{name}}", "{{app_name}}");
                            $replace = array($contact->first_name . " " . $contact->last_name, settingValue('site_title'));
                            $changed_message = str_replace($search, $replace, $emailTemplate->html_content);

                            // replace all valid hrefs with site url.
                            $jobTemplateDetails = $changed_message;
                            $linkDom = new DOMDocument;
                            $linkDom->loadHTML($jobTemplateDetails);
                            $allLinks = $linkDom->getElementsByTagName('a');
                            foreach ($allLinks as $rawLink) {
                                $oldLink = $rawLink->getAttribute('href');
                                if (filter_var($oldLink, FILTER_VALIDATE_URL)) {
                                    // valid url
                                    $newLink = url('click-campaign') . "?_id=" . Hashids::encode($campaign_id) . "&referal_id=" . Hashids::encode($contact->id) . "&history_id=" . Hashids::encode($campaignHistory->id) . "&redirect_to=" . $oldLink;
                                    $rawLink->setAttribute('href', $newLink);
                                }
                            }
                            $linkDom->saveHTML();

                            SendCapmaignMail::dispatch([$campaign->id, $contact->id, $campaignHistory->id], [$request["sender_name"], $request["sender_email"], $request["reply_to_email"]], $contact->email, $subject, $linkDom->saveHTML() . "<img src=" . url('track-campaign') . "?_id=" . Hashids::encode($campaign_id) . "&referal_id=" . Hashids::encode($contact->id) . "&history_id=" . Hashids::encode($campaignHistory->id) . " style='width:1px;height:1px; display:none;'>");

                            try {
                                SendCapmaignMail::dispatch([$campaign->id, $contact->id, $campaignHistory->id], [$request["sender_name"], $request["sender_email"], $request["reply_to_email"]], $contact->email, $subject, $linkDom->saveHTML() . "<img src=" . url('track-campaign') . "?_id=" . Hashids::encode($campaign_id) . "&referal_id=" . Hashids::encode($contact->id) . "&history_id=" . Hashids::encode($campaignHistory->id) . " style='width:1px;height:1px; display:none;'>");
                                EmailSendingLog::create([
                                    'user_id' => $user->id,
                                    'campaign_id' => $campaign->id,
                                    'subject_id' => $subject_id,
                                    'content_id' => $content_id,
                                    'contact_id' => $contact->id,
                                    'history_id' => $campaignHistory->id,
                                    'sent_at' => now()
                                ]);
                                // if ($user->package_subscription_id) {
                                //     $subscription = PackageSubscription::where('id', $user->package_subscription_id)->where('is_active', 1)->first();
                                //     if ($subscription) {
                                //         $subscription->update(['email_used' => $subscription->email_used + 1]);
                                //         if ($subscription->package_id == 9) {
                                //             $subscription->emails_paying_for += 1;
                                //             if ($subscription->email_used >= $email_span1_start && $subscription->email_used <= $email_span1_end) {
                                //                 $subscription->emails_to_pay += $email_span1_price;
                                //             } else if ($subscription->email_used >= $email_span2_start && $subscription->email_used <= $email_span2_end) {
                                //                 $subscription->emails_to_pay += $email_span2_price;
                                //             } else if ($subscription->email_used >= $email_span3_start) {
                                //                 $subscription->emails_to_pay += $email_span3_price;
                                //             }
                                //             $subscription->save();
                                //         }
                                //     }
                                // }
                            } catch (\Throwable $th) {
                                \Log::info('split email error: ', array(
                                    'error' => $th
                                ));
                                EmailSendingLog::create([
                                    'user_id' => $user->id,
                                    'campaign_id' => $campaign->id,
                                    'subject_id' => $subject_id,
                                    'content_id' => $content_id,
                                    'contact_id' => $contact->id,
                                    'history_id' => $campaignHistory->id,
                                    'failed_at' => now()
                                ]);
                            }
                        }
                    }
                }
                //campaign sent at
                $campaignHistory->update([
                    'updated_at' => now(),
                ]);
                $campaign->update([
                    'status' => 4
                ]);

                if (isset($request['callback_url'])) {
                    try {
                        $callback_client = new Client();
                        $callback_client->request('POST', $request['callback_url'], [
                            'form_params' => [
                                '_id' =>  Hashids::encode($campaign->id),
                                'campaign_type' => "Split testing Campaign",
                                'status' => '1',
                            ]
                        ]);
                    } catch (\Throwable $th) {
                        //throw $th;
                    }
                }

                // notify user of completion
                Notification::create([
                    'user_id' => $campaign->user_id,
                    'item_id' => $campaign->id,
                    'module' => 4,
                    'notification_type' => 3,
                    'notification_text' => "campaign_sent '" . $campaign->name . "'",
                    'redirect_to' => "/split-testing/" . \Hashids::encode($campaign->id) . "/report",
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
                $replace = array($name, TranslationHandler::getTranslation($user->language, 'Split Campaigns'), $campaign->name, $created_at, $updated_at, $sent_at, settingValue('site_title'));
                $content  = str_replace($search, $replace, $content);

                SendMail::dispatch($email, $subject, $content);
            }

            // set 'status' => 5 if no more sending needed

            // set next queue job if any recursive job remaining
            if ($campaign->campaign_type == 3) {

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

                        SplitTestingJob::dispatch($user, $request, $campaign->id, $this->job_code)->delay($totalDuration);
                    } elseif ($request['recursive_campaign_type'] == 2) {
                        //Monthly
                        $month = Carbon::now()->format('F');
                        $year = Carbon::now()->format('Y');
                        $day = $request['day_of_month'];

                        $selectedDay = Carbon::parse($month . ' ' . $day . ' ' . $year);
                        $selectedDay = $selectedDay->addMonth();

                        $scheduleTime = Carbon::parse($selectedDay);
                        $currentTime = Carbon::parse(now()->format('Y-m-d'));
                        $totalDuration = $currentTime->diff($scheduleTime);

                        SplitTestingJob::dispatch($user, $request, $campaign->id, $this->job_code)->delay($totalDuration);
                    } elseif ($request['recursive_campaign_type'] == 3) {
                        //Yearly
                        $month = config('constants.months_of_year')[$request['month_of_year']];
                        $day = $request['day_of_month'];
                        $year = date('Y');

                        $selectedDay = Carbon::parse($month . ' ' . $day . ' ' . $year)->addMonth(12)->startOfMonth();

                        $scheduleTime = Carbon::parse($selectedDay);
                        $currentTime = Carbon::parse(now()->format('Y-m-d'));
                        $totalDuration = $currentTime->diff($scheduleTime);

                        SplitTestingJob::dispatch($user, $request, $campaign->id, $this->job_code)->delay($totalDuration);
                    }
                } else {
                    $campaign->update([
                        'status' => 5
                    ]);
                }
            } else {
                $campaign->update([
                    'status' => 5
                ]);
            }
        }
    }
}
