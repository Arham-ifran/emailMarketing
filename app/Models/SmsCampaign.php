<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Contact;
use App\Models\SmsCampaignRecursion;
use App\Models\SmsCampaignSchedule;
use App\Models\Group;
use Psy\Command\HistoryCommand;

class SmsCampaign extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'name',
        'message',
        'type',
        'schedule_date',
        'recursive_campaign_type',
        'day_of_week',
        'day_of_month',
        'month_of_year',
        'no_of_time',
        'group_id', // being used to store id number of last added contact while campaign creation
        'group_ids',
        'recursive',
        'scheduled',
        'sending_started_at',
        'sending_completed_at',
        'sending_stopped_at',
        'send_to_group',
        'sender_name',
        'sender_number',
        'track_opens',
        'track_clicks',
        'status',
        'sending_to',
        'request_source',
        'job_code',
        'subscription_id'
    ];

    protected $cast = ['group_ids' => 'array'];

    /**
     * Filter the scope of the query for api.
     *
     * @return mixed
     */
    public function scopeFromApi($query)
    {
        return $query->where('request_source', 2);
    }

    /**
     * Filter the scope of the query for site.
     *
     * @return mixed
     */
    public function scopeFromSite($query)
    {
        return $query->where('request_source', 1);
    }

    public function contacts()
    {
        return $this->belongsToMany(Contact::class, 'campaign_contacts', 'campaign_id')->wherePivot('type', 1);
    }

    public function excludes()
    {
        return $this->belongsToMany(Contact::class, 'campaign_excludes', 'campaign_id')->wherePivot('deleted_at', null)->wherePivot('type', 1);
    }

    public function unsubscribers()
    {
        if ($this->sending_to == 1) {
            $grp = $this->group_id;
            $contacts = $this->belongsTo(Group::class, 'group_id')->with('contacts');
        } elseif ($this->sending_to == 2) {
            $contacts = $this->belongsToMany(Contact::class, 'sms_campaign_contacts')->wherePivot('deleted_at', null)->where('subscribed', 0);
        } else {
            $contacts = [];
        }
        return $contacts;
    }

    public function groups()
    {
        return $this->belongsToMany(Group::class, 'sms_campaign_groups')->wherePivot('deleted_at', null);
    }

    public function recursion()
    {
        return $this->hasOne(SmsCampaignRecursion::class)->where('deleted_at', null);
    }

    public function schedule()
    {
        return $this->hasOne(SmsCampaignSchedule::class)->where('deleted_at', null);
    }

    public function reports()
    {
        return $this->hasMany(CampaignHistory::class, 'campaign_id')->where('type', 1)->with('sms_fail')->with('sms_success');
    }
}
