<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Group;
use App\Models\SmsCampaign;
use Hashids;

class Contact extends Model
{
    use HasFactory, SoftDeletes;
    // protected $hidden = ['pivot'];

    protected $fillable = [
        'user_id', 'first_name', 'last_name', 'for_sms', 'for_email', 'country_code', 'number', 'email', 'subscribed', 'unsubscribed_at', 'confirmed_at', 'status', 'request_source'
    ];

    protected $appends = ['hash_id'];
    public function getHashidAttribute()
    {
        return Hashids::encode($this->id);
    }

    /**
     * Filter the scope of the query for api.
     *
     * @return mixed
     */
    public function scopeFromApi($query)
    {
        return $query->where('request_source', 2);
    }

    /**
     * Filter the scope of the query for site.
     *
     * @return mixed
     */
    public function scopeFromSite($query)
    {
        return $query->where('request_source', 1);
    }

    public function groups()
    {
        return $this->belongsToMany(Group::class, 'contact_groups')->wherePivot('deleted_at', null);
    }

    public function sms_campaigns()
    {
        return $this->belongsToMany(SmsCampaign::class, 'sms_campaign_contacts')->wherePivot('deleted_at', null);
    }
}
