<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SmsCampaignFails extends Model
{
    use HasFactory;
    protected $table = 'sms_campaign_fails';

    protected $fillable = [
        'sms_campaign_id',
        'contact_id',
        'sent_at',
        'failed_at',
    ];
}
