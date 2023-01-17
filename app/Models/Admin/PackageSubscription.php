<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use Carbon\Carbon;

class PackageSubscription extends Model
{
  protected $fillable = [
    'user_id', 'package_id', 'price', 'features', 'description', 'type', 'start_date', 'end_date', 'repetition', 'payment_option', 'is_active', 'contact_limit', 'email_limit', 'email_used', 'sms_limit', 'sms_used', 'emails_paying_for', 'emails_to_pay', 'sms_paying_for', 'sms_to_pay', 'contacts_paying_for', 'contacts_to_pay'
  ];

  // ************************** //
  //        Relationships       //
  // ************************** //

  public function user()
  {
    return $this->belongsTo('App\Models\User', 'user_id');
  }

  public function package()
  {
    return $this->belongsTo(Package::class, 'package_id');
  }

  public function payment()
  {
    return $this->hasOne(Payment::class, 'subscription_id');
  }

  // ************************** //
  //        	Attributes        //
  // ************************** //

  public function getPackageImageAttribute()
  {
    return $this->attributes['package_image'] = checkImage(asset('storage/packages/' . $this->package->icon), 'placeholder.png');
  }

  public function getPackageTitleAttribute()
  {
    return $this->attributes['package_title'] = $this->package->title;
  }

  public function getLinkedFeaturesAttribute()
  {
    $features = json_decode($this->features, true);
    $linked_features = [];

    if (!empty($features)) {
      foreach ($features as $key => $value) {
        $packageFeature = PackageFeature::find($key);

        $arr['id']   = $key;
        $arr['name'] = $packageFeature->name;
        $arr['info'] = $packageFeature->info;
        $arr['count'] = $value;

        $linked_features[] = $arr;
      }
    }

    return $this->attributes['linked_features'] = $linked_features;
  }
}
