<?php

namespace App\Console\Commands;

use App\Jobs\SendMail;
use App\Models\Admin\EmailTemplate;
use App\Models\Admin\Notification;
use App\Models\Admin\PackageSubscription;
use App\Models\Admin\Payment;
use App\Models\CampaignContact;
use App\Models\CampaignExclude;
use App\Models\CampaignHistory;
use App\Models\Contact;
use App\Models\Contact_group;
use App\Models\EmailCampaign;
use App\Models\EmailCampaignClick;
use App\Models\EmailCampaignLogs;
use App\Models\EmailCampaignOpen;
use App\Models\EmailCampaignTemplate;
use App\Models\EmailSendingLog;
use App\Models\Group;
use App\Models\PayAsYouGoPayments;
use App\Models\SmsCampaign;
use App\Models\SmsCampaignGroup;
use App\Models\SmsCampaignLogs;
use App\Models\SmsTemplate;
use App\Models\SplitTestSubject;
use App\Models\User;
use App\Models\User_log;
use Carbon\Carbon;
use Illuminate\Console\Command;

class userFollowUp extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:inactivity-follow-up';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for inactive users and send them email telling them that their account will be set to inactive in admin defined days.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // return Command::SUCCESS;
        $user_deletion_days = settingValue('user_deletion_days');
        $user_inactivation_days = settingValue('user_inactivation_days');
        $user_soft_deletion_days = settingValue('user_soft_deletion_days');

        $first = settingValue('first_notification');
        $second = settingValue('second_notification');
        $third = settingValue('third_notification');

        $users = User::all();

        foreach ($users as $user) {
            // check last active
            if ($user->last_active_at) {
                $now = Carbon::parse(now());
                $last_active = Carbon::parse($user->last_active_at);
                $logged_days_ago = $last_active->diffInDays($now);
                if ($user->status == 1 && $user_inactivation_days) {
                    // set to inactive 
                    $sendMail = false;
                    $action = "Inactive";
                    $days = 0;
                    // warning messages
                    if ($first && $now->format('Y-m-d') == Carbon::parse($user->last_active_at)->addDays($user_inactivation_days - $first)->format('Y-m-d')) {
                        $sendMail = true;
                        $days = $first;
                    } else if ($second && $now->format('Y-m-d') == Carbon::parse($user->last_active_at)->addDays($user_inactivation_days - $second)->format('Y-m-d')) {
                        $sendMail = true;
                        $days = $second;
                    } else if ($third && $now->format('Y-m-d') == Carbon::parse($user->last_active_at)->addDays($user_inactivation_days - $third)->format('Y-m-d')) {
                        $sendMail = true;
                        $days = $third;
                    }

                    // send email:
                    if ($sendMail) {
                        $email_template = EmailTemplate::where('type', 'account_inactive')->first();
                        $email_template = transformEmailTemplateModel($email_template, $user->language);
                        $subject = $email_template['subject'];
                        $content = $email_template['content'];
                        $link = url('/signin');
                        $contact_link = url('/contact-us');
                        $search = array("{{name}}", "{{days}}", "{{link}}", "{{contact_link}}", "{{app_name}}");
                        $replace = array($user->name, $days, $link, $contact_link, settingValue('site_title'));
                        $content = str_replace($search, $replace, $content);
                        SendMail::dispatch($user->email, $subject, $content);
                    }

                    if ($last_active->diffInDays($now) == $user_inactivation_days) {
                        // doing and notifying finally
                        $user->update(['status' => 0, 'disabled_at' => date("Y-m-d H:i:s")]);
                        // sendemail
                        $email_template = EmailTemplate::where('type', 'account_info')->first();
                        $email_template = transformEmailTemplateModel($email_template, $user->language);
                        $subject = $email_template['subject'];
                        $content = $email_template['content'];
                        $contact_link = url('/contact-us');
                        $search = array("{{name}}", "{{contact_link}}", "{{no_of_days}}", "{{app_name}}");
                        $replace = array($user->name, $contact_link, $user_inactivation_days, settingValue('site_title'));
                        $content = str_replace($search, $replace, $content);
                        SendMail::dispatch($user->email, $subject, $content);
                    }
                } else if ($user->status == 0 && $user_soft_deletion_days) {
                    // set to soft delete

                    $sendMail = false;
                    $action = "Deleted";
                    $days = 0;
                    // warning messages
                    if ($first && $now->format('Y-m-d') == Carbon::parse($user->last_active_at)->addDays($user_inactivation_days + $user_soft_deletion_days - $first)->format('Y-m-d')) {
                        $sendMail = true;
                        $days = $first;
                    } else if ($second && $now->format('Y-m-d') == Carbon::parse($user->last_active_at)->addDays($user_inactivation_days + $user_soft_deletion_days - $second)->format('Y-m-d')) {
                        $sendMail = true;
                        $days = $second;
                    } else if ($third && $now->format('Y-m-d') == Carbon::parse($user->last_active_at)->addDays($user_inactivation_days + $user_soft_deletion_days - $third)->format('Y-m-d')) {
                        $sendMail = true;
                        $days = $third;
                    }

                    // send email:
                    // if ($sendMail) {
                    //     $email_template = EmailTemplate::where('type', 'account_inactive')->first();
                    // $email_template = transformEmailTemplateModel($email_template, $user->language);
                    //     $subject = $email_template->subject;
                    //     $content = $email_template->content;
                    //     $link = url('/signin');
                    //     $search = array("{{name}}", "{{link}}", "{{app_name}}", "{{action}}", "{{days}}");
                    //     $replace = array($user->name, $link, settingValue('site_title'), $action, $days);
                    //     $content = str_replace($search, $replace, $content);
                    //     SendMail::dispatch($user->email, $subject, $content);
                    // }

                    if ($last_active->diffInDays($now) == $user_inactivation_days + $user_soft_deletion_days) {
                        // doing and notifying finally
                        $user->update(['status' => 3, 'deleted_at' => date("Y-m-d H:i:s")]);

                        // // sendemail
                        // $email_template = EmailTemplate::where('type', 'account_info')->first();
                        // $email_template = transformEmailTemplateModel($email_template, $user->language);
                        // $subject = $email_template->subject;
                        // $content = $email_template->content;
                        // $link = url('/signin');
                        // $search = array("{{name}}", "{{link}}", "{{app_name}}", "{{action}}");
                        // $replace = array($user->name, $link, settingValue('site_title'), $action);
                        // $content = str_replace($search, $replace, $content);
                        // SendMail::dispatch($user->email, $subject, $content);
                    }
                } else if ($last_active->diffInDays($now) == $user_inactivation_days + $user_soft_deletion_days) {
                    // delete all user data

                    // tables having user data:
                    CampaignContact::where('user_id', $user->id)->delete();
                    CampaignExclude::where('user_id', $user->id)->delete();
                    CampaignHistory::where('user_id', $user->id)->delete();
                    Contact_group::where('user_id', $user->id)->delete();
                    Contact::where('user_id', $user->id)->delete();
                    CampaignHistory::where('user_id', $user->id)->delete();
                    EmailCampaign::where('user_id', $user->id)->delete();
                    // EmailCampaignClick::where('user_id', $user->id)->delete();
                    // EmailCampaignOpen::where('user_id', $user->id)->delete();
                    EmailCampaignLogs::where('user_id', $user->id)->delete();
                    EmailCampaignTemplate::where('user_id', $user->id)->delete();
                    EmailSendingLog::where('user_id', $user->id)->delete();
                    Group::where('user_id', $user->id)->delete();
                    Notification::where('user_id', $user->id)->delete();
                    PackageSubscription::where('user_id', $user->id)->delete();
                    Payment::where('user_id', $user->id)->delete();
                    PayAsYouGoPayments::where('user_id', $user->id)->delete();
                    SmsCampaign::where('user_id', $user->id)->delete();
                    SmsCampaignLogs::where('user_id', $user->id)->delete();
                    SmsTemplate::where('user_id', $user->id)->delete();
                    SplitTestSubject::where('user_id', $user->id)->delete();
                    User_log::where('user_id', $user->id)->delete();

                    // delete user
                    $user->delete();
                }

                // print_r($user->name . " was last logged in: " . $logged_days_ago . " days ago.");
                // print_r("\n");
            }
        }
    }
}
