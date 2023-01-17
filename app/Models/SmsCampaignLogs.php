<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SmsCampaignLogs extends Model
{
    use HasFactory;
    protected $table = 'sms_logs';

    protected $fillable = [
        'user_id',
        'to',
        'from',
        'body',
        'started_at',
        'sent_at',
        'failed_at',
        'sid',
        'apiVersion',
        'failed_reason',
        'history_id',
        'contact_id',
        'campaign_id'
    ];
}
