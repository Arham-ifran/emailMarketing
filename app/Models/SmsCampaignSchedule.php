<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Contact;
use App\Models\Group;
use App\Models\SmsCampaign;

class SmsCampaignSchedule extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'sms_campaign_id',
        'scheduled_at',
        'sent',
        'status',
    ];

    public function sms_campaign()
    {
        return $this->belongsTo(SmsCampaign::class)->with('contacts')->where('deleted_at', null);
    }
}
