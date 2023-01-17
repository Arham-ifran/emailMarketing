<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
  protected $fillable = [
    'user_id', 'subscription_id', 'item', 'payment_method', 'amount', 'vat_percentage', 'vat_amount', 'vat_country_code', 'discount_percentage', 'discount_amount', 'reseller', 'voucher', 'total_amount', 'payload', 'invoice', 'token', 'txn_id', 'payer_id', 'data', 'payment_mode', 'lang', 'profile_id', 'profile_data', 'timestamp', 'status'
  ];

  // ************************** //
  //        Relationships       //
  // ************************** //

  public function user()
  {
    return $this->belongsTo('App\Models\User', 'user_id');
  }

  public function subscription()
  {
    return $this->belongsTo(PackageSubscription::class, 'subscription_id');
  }
}
