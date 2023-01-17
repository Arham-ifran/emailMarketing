<?php

namespace App\Console\Commands;

use App\Jobs\SendMail;
use App\Models\Admin\EmailTemplate;
use App\Models\Admin\Notification;
use App\Models\CampaignContact;
use App\Models\CampaignExclude;
use App\Models\CampaignHistory;
use App\Models\Contact;
use App\Models\Contact_group;
use App\Models\EmailCampaign;
use App\Models\EmailCampaignLogs;
use App\Models\EmailCampaignTemplate;
use App\Models\EmailSendingLog;
use App\Models\Group;
use App\Models\SmsCampaign;
use App\Models\SmsCampaignGroup;
use App\Models\SmsCampaignLogs;
use App\Models\SplitTestSubject;
use App\Models\User;
use App\Models\User_log;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CroneJobWorkingNotification  extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:crone-email';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check Crone job working and send notification email.';

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
        SendMail::dispatch('hamna.farooq@arhamsoft.org', 'Checking Crone Jobs', "Your crone Jobs are working.");
    }
}
