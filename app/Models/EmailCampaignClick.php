<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailCampaignClick extends Model
{
    use HasFactory;
    protected $fillable = ['campaign_id', 'contact_id', 'history_id', 'link'];
    protected $table = 'email_campaign_click_links';

    public function contact()
    {
        return $this->belongsTo(Contact::class, 'contact_id');
    }

    public function campaign()
    {
        return $this->belongsTo(EmailCampaign::class, 'campaign_id');
    }
}
