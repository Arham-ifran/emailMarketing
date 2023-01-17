<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Admin\Package;
use Illuminate\Support\Facades\Storage;
use DB;

class MonthlyQuotaRevision extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'monthly_quota:users';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'In case a user selects Annual subscription, his assets will be renewed each month.';

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
        $current_date = Carbon::now('UTC')->subMonths(1)->format('Y-m-d');
        $users = User::whereNotNull('last_quota_revised')->whereRaw('DATE(last_quota_revised) = ?', $current_date)->get();

        if (!$users->isEmpty()) {
            foreach ($users as $user) {
                $package = Package::find($user->package_id);
                $packageLinkedFeatures = $package->linkedFeatures->pluck('count', 'feature_id')->toArray();

                $user->subscription->update([
                    'contact_limit' => $packageLinkedFeatures[3],
                    'email_limit' => $packageLinkedFeatures[1],
                    'sms_limit' => $packageLinkedFeatures[2],
                    'email_used' => 0,
                    'sms_used' => 0,
                ]);
                $user->update([
                    // 'total_allocated_space'    => $packageLinkedFeatures[1],
                    // 'remaining_allocated_space'=> $packageLinkedFeatures[1] * 1073741824, // Multiply With 1 GB
                    // 'max_file_size'            => $packageLinkedFeatures[2],
                    'last_quota_revised'       => date("Y-m-d H:i:s")
                ]);
            }
        }
    }
}
