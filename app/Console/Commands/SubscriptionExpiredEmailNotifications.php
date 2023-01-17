<?php

namespace App\Console\Commands;

use App\Jobs\SendMail;
use Illuminate\Console\Command;
use Carbon\Carbon;
use App\Models\User;
use App\Models\EmailTemplate;
use Illuminate\Support\Facades\Storage;
use DB;

class SubscriptionExpiredEmailNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscription_expired:notifications';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send email notifications about subscription expired to users after specific number of days.';

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
     * @return mixed
     */
    public function handle()
    {
        $first_notification_days =  settingValue('subscription_expiry_first_notification');
        $second_notification_days =  settingValue('subscription_expiry_second_notification');
        $third_notification_days =  settingValue('subscription_expiry_third_notification');
        if (isset($first_notification_days) || isset($second_notification_days) || isset($third_notification_days)) {
            $first_notification_date = isset($first_notification_days) ? Carbon::now('UTC')->addDays($first_notification_days)->format('Y-m-d') : '1970-01-01';
            $second_notification_date = isset($second_notification_days) ? Carbon::now('UTC')->addDays($second_notification_days)->format('Y-m-d') : '1970-01-01';
            $third_notification_date = isset($third_notification_days) ? Carbon::now('UTC')->addDays($third_notification_days)->format('Y-m-d') : '1970-01-01';

            $users = User::where('package_recurring_flag', '!=', 1)->whereHas('subscription', function ($query) use ($first_notification_date, $second_notification_date, $third_notification_date) {
                return $query->whereRaw('DATE(FROM_UNIXTIME(end_date)) = ?', $first_notification_date)->orWhereRaw('DATE(FROM_UNIXTIME(end_date)) = ?', $second_notification_date)->orWhereRaw('DATE(FROM_UNIXTIME(end_date)) = ?', $third_notification_date);
            })->get();

            if (!$users->isEmpty()) {
                foreach ($users as $user) {
                    $end_date = $user->subscription->end_date;
                    $end_date_human_readable = Carbon::createFromTimeStamp($end_date, "UTC")->format('d M, Y');
                    $end_date = Carbon::createFromTimeStamp($end_date, "UTC")->format('Y-m-d');

                    $remaining_no_of_days = 0;

                    if ($first_notification_date == $end_date) {
                        $remaining_no_of_days = $first_notification_days;
                    } else if ($second_notification_date == $end_date) {
                        $remaining_no_of_days = $second_notification_days;
                    } else if ($third_notification_date == $end_date) {
                        $remaining_no_of_days = $third_notification_days;
                    }

                    if ($remaining_no_of_days != 0) {
                        // **********************************************************************  //
                        // Send Email About subscription expiry notification after number of days  //
                        // **********************************************************************  //

                        if ($remaining_no_of_days == 1) {
                            $remaining_no_of_days = 'tomorrow';
                        } else {
                            $remaining_no_of_days = 'in ' . $remaining_no_of_days . ' days';
                        }

                        $email_template = EmailTemplate::where('type', 'packages_expiry_follow_up_email')->first();
                        $email_template = transformEmailTemplateModel($email_template, $user->language);
                        $name = $user->name;
                        $email = $user->email;
                        $upgrade_link = url('/packages/upgrade-package');
                        $contact_link = url('/contact-us');
                        $subject = $email_template['subject'];
                        $content = $email_template['content'];

                        $search = array("{{name}}", "{{remaining_days}}", "{{expiry_date}}", "{{upgrade_link}}", "{{contact_link}}", "{{app_name}}");
                        $replace = array($name, $remaining_no_of_days, $end_date_human_readable, $upgrade_link, $contact_link, settingValue('site_title'));
                        $content  = str_replace($search, $replace, $content);

                        SendMail::dispatch($email, $subject, $content);
                    }
                }
            }
        }
    }
}
