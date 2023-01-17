<?php

namespace App\Models;

use App\Models\Admin\PackageSubscription;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PayAsYouGoPayments extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'package_subscription_id',
        'link',
        'charging_for_emails',
        'charging_for_sms',
        'charging_for_contacts',
        'price_for_emails_charged',
        'price_for_sms_charged',
        'price_for_contacts_charged',
        'amount',
        'vat_percentage',
        'vat_amount',
        'vat_country_code',
        'total_amount_charged',

        'payload',
        'invoice',
        'token',
        'txn_id',
        'payer_id',
        'data',
        'payment_mode',
        'lang',
        'profile_id',
        'profile_data',
        'timestamp',
        'status',
        'discount_percentage',
        'discount_amount',
        'payment_method'
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
        return $this->belongsTo(PackageSubscription::class, 'package_subscription_id');
    }
}
