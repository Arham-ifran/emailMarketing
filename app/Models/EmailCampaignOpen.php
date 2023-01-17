<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailCampaignOpen extends Model
{
    use HasFactory;
    protected $fillable = ['campaign_id', 'contact_id', 'history_id'];
    protected $table = 'email_campaign_opens';

    public function contact()
    {
        return $this->belongsTo(Contact::class, 'contact_id');
    }

    public function children()
    {
        return $this->hasMany(EmailCampaignOpen::class, 'contact_id', 'contact_id', 'campaign_id');
    }
}
