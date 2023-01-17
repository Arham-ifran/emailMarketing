<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailCampaignLogs extends Model
{
    use HasFactory;
    protected $table = 'email_sending_logs';

    protected $fillable = [
        'user_id',
        'campaign_id',
        'contact_id',
        'sent_at',
        'opened_at',
        'bounced_at',
        'unsubscribed_at',
        'failed_at',
        'failed_reason'
    ];
}
