<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmailCampaign extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'user_id',
        'name',
        'subject',
        'sender_name',
        'sender_email',
        'reply_to_email',
        'track_opens',
        'track_clicks',
        'template_id',
        'campaign_type',
        'schedule_date',
        'recursive_campaign_type',
        'day_of_week',
        'day_of_month',
        'month_of_year',
        'day_of_week_year',
        'no_of_time',
        'group_id', // being used to store id number of last added contact while campaign creation
        'group_ids',
        'is_split_testing',
        'split_test_param',
        'split_subject_line_1',
        'split_subject_line_2',
        'split_email_content_1',
        'split_email_content_2',
        'size_of_group',
        'status', // 1=Active,2=Draft,3=Delete,4=Sending,5=Sent,6=Stopped
        'request_source',
        'job_code',
        'sending_stopped_at',
        'subscription_id'
    ];
    protected $cast = ['group_ids' => 'array'];

    protected static function boot()
    {
        parent::boot();

        static::updating(function ($model) {
            $model->updated_at = date('Y-m-d H:i:s');
        });

        static::creating(function ($model) {
            if (isset($model->schedule_date) && !empty($model->schedule_date)) {
                $model->schedule_date = date('Y-m-d H:i:s', strtotime($model->schedule_date));
            }
            $model->created_at = date('Y-m-d H:i:s');
            if (auth()->user())
                $model->user_id = auth()->user()->id;
        });
    }

    /**
     * relation that tells if the campaign is sent.
     *
     * @return mixed
     */
    public function sent()
    {
        return $this->hasMany(CampaignHistory::class, 'campaign_id')->where('type', 1)->with('fail')->with('success');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * relation sends the subscribers list of the campaign
     *
     * @return mixed
     */
    // public function subscribers()
    // {
    //     $contacts = Group::where('id', $this->group_id)->with('contacts');
    //     return $contacts->where('subscribed', 1);
    // }

    /**
     * relation sends the unsubscribers list of the campaign
     *
     * @return mixed
     */
    // public function unsubscribers()
    // {
    //     $contacts = $this->belongsTo(Group::class, 'group_id')->with('contacts');
    //     return $contacts->where('subscribed', 0);
    // }

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

    /**
     * relation that sends the contacts excluded from the campaign.
     *
     * @return mixed
     */
    public function excludes()
    {
        return $this->belongsToMany(Contact::class, 'campaign_excludes', 'campaign_id')->wherePivot('deleted_at', null)->wherePivot('type', 2);
    }

    public function reports()
    {
        return $this->hasMany(CampaignHistory::class, 'campaign_id')->where('type', 1)->with('fail')->with('success');
    }

    public function contacts()
    {
        return $this->belongsToMany(Contact::class, 'campaign_contacts', 'campaign_id')->wherePivot('type', 2);
    }
}
