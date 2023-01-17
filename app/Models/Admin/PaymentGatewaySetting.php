<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class PaymentGatewaySetting extends Model
{
    protected $fillable = [
        'paypal_sandbox_api_username',
        'paypal_sandbox_api_password',
        'paypal_sandbox_api_secret',
        'paypal_sandbox_api_base_url',
        'paypal_live_api_username',
        'paypal_live_api_password',
        'paypal_live_api_secret',
        'paypal_live_api_base_url',
        'paypal_mode',
        'paypal_status',
        'wirecard_sandbox_api_username',
        'wirecard_sandbox_api_password',
        'wirecard_sandbox_api_merchant_id',
        'wirecard_sandbox_api_base_url',
        'wirecard_live_api_username',
        'wirecard_live_api_password',
        'wirecard_live_api_merchant_id',
        'wirecard_live_api_base_url',
        'wirecard_mode',
        'wirecard_status',
        'klarna_sandbox_api_username',
        'klarna_sandbox_api_password',
        'klarna_sandbox_api_base_url',
        'klarna_live_api_username',
        'klarna_live_api_password',
        'klarna_live_api_base_url',
        'klarna_mode',
        'klarna_status',
        'mollie_sandbox_api_key',
        'mollie_live_api_key',
        'mollie_mode',
        'mollie_status',

    ];
}
