<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CampaignContact extends Model
{
    use HasFactory;

    protected $fillable = [
        'contact_id', 'campaign_id', 'user_id', 'type'
    ];
}
