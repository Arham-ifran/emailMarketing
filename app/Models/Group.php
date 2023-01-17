<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Contact;
use App\Models\SmsCampaign;
use Hashids;

class Group extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id', 'name', 'for_sms', 'for_email', 'sender_name', 'sender_email', 'double_opt_in', 'status', 'description', 'request_source'
    ];

    protected $hidden = ['pivot'];
    protected $appends = ['hash_id'];
    public function getHashidAttribute()
    {
        return Hashids::encode($this->attributes['id']);
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

    public function contacts()
    {
        return $this->belongsToMany(Contact::class, 'contact_groups')->wherePivot('deleted_at', null);
        // return $this->belongsToMany(Contact::class, 'contact_groups', 'group_id', 'contact_id');
        // return Contact_group::where('group_id', $id)->with('contact')->get();
    }

    public function sms_campaigns()
    {
        return $this->belongsToMany(SmsCampaign::class, 'sms_campaign_groups')->wherePivot('deleted_at', null);
    }
}
