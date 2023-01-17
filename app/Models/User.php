<?php

namespace App\Models;

use App\Models\Admin\Package;
use App\Models\Admin\PackageSubscription;
use App\Models\Admin\Payment;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Contact;
use App\Models\Group;
use Illuminate\Database\Eloquent\Scope;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'original_password',
        'google_id',
        'facebook_id',
        'twitter_id',
        'status',
        'last_active_at',
        'disabled_at',
        'deleted_at',
        'secret_key',
        'api_token',
        'endpoint_urls',
        'api_status',
        'profile_image_path',
        'street',
        'city',
        'zip_code',
        'country_id',

        'timezone',
        'language',
        'is_expired',
        'expired_package_disclaimer',
        'payment_method',
        'package_id',
        'last_quota_revised',
        'on_hold_package_id',
        'prev_package_subscription_id',
        'package_subscription_id',
        'package_recurring_flag',
        'payment_id',
        'mollie_customer_id',
        'login_location',
        'ip_address',
        'on_trial',
        'switch_to_paid_package',
        'package_updated_by_admin',
        'unpaid_package_email_by_admin',
        'platform',
        'temp_zip_file'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Filter the scope of the query for api.
     *
     * @return mixed
     */
    public function scopeFromApi($query)
    {
        return $query->where('api', 2);
    }

    /**
     * Filter the scope of the query for site.
     *
     * @return mixed
     */
    public function scopeFromSite($query)
    {
        return $query->where('api', 1);
    }

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    // user's contacts
    public function contacts()
    {
        return $this->hasMany(Contact::class)->where('deleted_at', null);
    }
    // user's groups
    public function groups()
    {
        return $this->hasMany(Group::class)->where('deleted_at', null);
    }

    // package relations

    public function package()
    {
        return $this->belongsTo(Package::class, 'package_id');
    }

    public function onHoldPackage()
    {
        return $this->belongsTo(Package::class, 'on_hold_package_id');
    }

    public function subscription()
    {
        return $this->belongsTo(PackageSubscription::class, 'package_subscription_id');
    }

    public function previousSubscription()
    {
        return $this->belongsTo(PackageSubscription::class, 'prev_package_subscription_id');
    }

    public function payment()
    {
        return $this->belongsTo(Payment::class, 'payment_id');
    }

    public function allSubscriptions()
    {
        return $this->hasMany(PackageSubscription::class, 'user_id');
    }

    public function country()
    {
        return $this->belongsTo('App\Models\Admin\Country', 'country_id');
    }

    public function usertimezone()
    {
        return $this->belongsTo('App\Models\Admin\Timezone', 'country_id');
    }

    // public function accountSettings()
    // {
    //     return $this->hasOne('App\Models\AccountSetting', 'user_id');
    // }
}
