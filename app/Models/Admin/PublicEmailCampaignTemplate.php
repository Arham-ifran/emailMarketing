<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class PublicEmailCampaignTemplate extends Model
{
    protected $fillable = [
        'name',
        'content',
        'html_content',
        'image',
        'status',
    ];

    protected $table = 'public_email_campaign_templates';
}
