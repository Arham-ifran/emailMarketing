<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SmsCampaignGroup extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'group_id', 'sms_campaign_id', 'user_id'
    ];
}
