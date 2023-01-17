<?php

namespace App\Jobs;

use App\CustomClasses\ExtendedZip;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use DataTables;
use Session;
use DB;
use File;
use Storage;
use PDF;
use Carbon\Carbon;
use ZipArchive;
use App\Models\Admin\PackageSubscription;
use App\Models\Admin\Payment;
use App\Models\EmailCampaign;
use App\Models\SmsCampaign;
use App\Models\User;

class ArchiveUserAllDataJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user_id;
    public $timeout = 21600; // timeout for job in seconds
    public $tries = 1;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($id)
    {
        $this->user_id = $id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $id = $this->user_id;
        $user = User::find($id);
        $data['user'] = $user;

        $data['payments'] = Payment::where('user_id', $id)->where('status', 1)->orderBy('created_at', 'DESC')->get();
        $data['subscriptions'] = PackageSubscription::where('user_id', $id)->orderBy('created_at', 'DESC')->get();
        $data['smscampaigns'] = SmsCampaign::where('user_id', $id)->where('status', '!=', 1)->orderBy('created_at', 'DESC')->get();
        $data['emailcampaigns'] = EmailCampaign::where('user_id', $id)->where('is_split_testing', 0)->where('status', '!=', 2)->orderBy('created_at', 'DESC')->get();
        $data['splitcampaigns'] = EmailCampaign::where('user_id', $id)->where('is_split_testing', 1)->where('status', '!=', 2)->orderBy('created_at', 'DESC')->get();

        //MAKE DIRECTORY
        $upload_path = 'public/temp/user-data-' . $user->email;
        if (!File::exists(public_path() . '/storage/temp/user-data-' . $user->email)) {
            Storage::makeDirectory($upload_path);
            if (File::exists(public_path() . '/storage/temp/user-data-' . $user->email)) {
                chmod(public_path() . '/storage/temp/user-data-' . $user->email, 0777);
            }
        }
        # Save PDFs to storage/temp/user-data subscription
        $pdf = PDF::loadView('admin.lawful-interception.user_details_pdf', $data)->save(public_path('/storage/temp/user-data-' . $user->email . '/' . $user->email . '-details.pdf'));

        if (count($data['payments']) > 0) {
            $pdf = PDF::loadView('admin.lawful-interception.user_payments_pdf', $data)->save(public_path('/storage/temp/user-data-' . $user->email . '/' . $user->email . '-payments.pdf'));
        }

        if (count($data['subscriptions']) > 0) {
            $pdf = PDF::loadView('admin.lawful-interception.user_subscriptions_pdf', $data)->save(public_path('/storage/temp/user-data-' . $user->email . '/' . $user->email . '-subscriptions.pdf'));
        }

        if (count($data['smscampaigns']) > 0) {
            $pdf = PDF::loadView('admin.lawful-interception.user_sms_pdf', $data)->save(public_path('/storage/temp/user-data-' . $user->email . '/' . $user->email . '-sms.pdf'));
        }

        if (count($data['emailcampaigns']) > 0) {
            $pdf = PDF::loadView('admin.lawful-interception.user_email_pdf', $data)->save(public_path('/storage/temp/user-data-' . $user->email . '/' . $user->email . '-email.pdf'));
        }

        if (count($data['splitcampaigns']) > 0) {
            $pdf = PDF::loadView('admin.lawful-interception.user_split_pdf', $data)->save(public_path('/storage/temp/user-data-' . $user->email . '/' . $user->email . '-split.pdf'));
        }

        # zip files recursivley folder/sub folder
        ExtendedZip::zipTree(public_path() . '/storage/temp/user-data-' . $user->email, $user, ZipArchive::CREATE);
    }
}
