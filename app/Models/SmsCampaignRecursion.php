<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Contact;
use App\Models\Group;
use App\Models\SmsCampaign;

class SmsCampaignRecursion extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'sms_campaign_id',
        'type',
        'days_apart',
        'month_number',
        'month_date',
        'week_day',
        'times_sent',
        'times_to_send',
        'scheduled_at',
        'status',
    ];

    public function sms_campaign()
    {
        return $this->belongsTo(SmsCampaign::class)->with('contacts')->where('deleted_at', null);
    }
}
