<?php

namespace App\Console\Commands;

use App\Jobs\SendMail;
use App\Models\Admin\Package;
use App\Models\EmailTemplate;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;

class PackageSubscriptionExpired extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'package_subscription:expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Package Subscription Expired and user downgraded to free package.';

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
        $users = User::where('package_id', '!=', 2)->get();

        foreach ($users as $user) {
            $subscription = $user->subscription;
            $currentTimestamp = Carbon::now('UTC')->timestamp;

            if (!empty($subscription->end_date) && $subscription->end_date < $currentTimestamp) {

                //************************//
                // Subscribe Free Package //
                //************************//

                $user->update([
                    'is_expired'                    => 0,
                    'on_hold_package_id'            => $subscription->package_id,
                    'prev_package_subscription_id'  => $subscription->id,
                    'package_recurring_flag'        => 0,
                    'switch_to_paid_package'        => 0,
                    'package_updated_by_admin'      => 0,
                    'unpaid_package_email_by_admin' => 0,
                    'expired_package_disclaimer'    => 1
                ]);

                $package = Package::find(2);
                activatePackage($user->id, $package);

                // ****************************************************//
                // Send Email About Package downgraded to free package  //
                // *************************************************** //

                $email_template = EmailTemplate::where('type', 'package_downgrade_after_subscription_expired')->first();
                $email_template = transformEmailTemplateModel($email_template, $user->language);
                $name = $user->name;
                $email = $user->email;
                $upgrade_link = url('/packages/upgrade-package');
                $contact_link = url('/contact-us');
                $subject = $email_template['subject'];
                $content = $email_template['content'];

                $search = array("{{name}}", "{{from}}", "{{to}}", "{{upgrade_link}}", "{{contact_link}}", "{{app_name}}");
                $replace = array($name, $subscription->package_title, $package->title, $upgrade_link, $contact_link, settingValue('site_title'));
                $content = str_replace($search, $replace, $content);

                SendMail::dispatch($email, $subject, $content);
            }
        }
    }
}
