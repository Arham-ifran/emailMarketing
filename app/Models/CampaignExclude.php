<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CampaignExclude extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'contact_id', 'campaign_id', 'user_id', 'type'
    ];
}
