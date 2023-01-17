<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Contact;
use phpDocumentor\Reflection\Types\This;

class CampaignHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'type', 'campaign_id', 'user_id'
    ];

    public function success()
    {
        return $this->belongsToMany(Contact::class, 'email_sending_logs', 'history_id')->wherePivot('failed_at', NULL);
    }
    public function sms_success()
    {
        return $this->belongsToMany(Contact::class, 'sms_logs', 'history_id')->wherePivot('sent_at', '!=', NULL);
    }

    public function bounces()
    {
        return $this->belongsToMany(Contact::class, 'email_sending_logs', 'history_id')->wherePivot('bounced_at', '!=', NULL);
    }

    public function fail()
    {
        return $this->belongsToMany(Contact::class, 'email_sending_logs', 'history_id')->wherePivot('failed_at', '!=', NULL);
    }

    public function sms_fail()
    {
        return $this->belongsToMany(Contact::class, 'sms_logs', 'history_id')->wherePivot('failed_at', '!=', NULL);
    }

    public function sent_to()
    {
        return $this->belongsToMany(Contact::class, 'email_sending_logs', 'history_id')->withPivot(['sent_at', 'failed_at', 'bounced_at', 'subject_id', 'content_id']);
    }

    public function sms_sent_to()
    {
        return $this->belongsToMany(Contact::class, 'sms_logs', 'history_id')->withPivot(['started_at', 'sent_at', 'failed_at']);
    }

    public function unsubscribers()
    {
        return $this->belongsToMany(Contact::class, 'email_sending_logs', 'history_id')->where('subscribed', 0);
    }

    public function sms_unsubscribers()
    {
        return $this->belongsToMany(Contact::class, 'sms_logs', 'history_id')->where('subscribed', 0);
    }
}
