<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PublicEmailCampaignTemplateLabel extends Model
{
    protected $fillable = [
        'email_campaign_template_id', 'label', 'value', 'status'
    ];
    protected $table = 'public_email_campaign_template_labels';

    public function emailTemplate()
    {
        return $this->belongsTo('App\Models\Admin\PublicEmailCampaignTemplate', 'email_campaign_template_id');
    }
}
