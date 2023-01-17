<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Encryption\DecryptException;

class AccountSetting extends Model
{
    protected $fillable = [
        'user_id', 'address', 'card_holder_name', 'card_brand', 'card_number', 'card_last_four_digits', 'expire_month', 'expire_year', 'cvc'
    ];

    public function getCardNumberAttribute($value)
    {
        try {
            return decrypt($value);
        } catch (DecryptException $e) {
            return $value;
        }
    }

    public function getCvcAttribute($value)
    {
        try {
            return decrypt($value);
        } catch (DecryptException $e) {
            return $value;
        }
    }

    // ************************** //
    //        Relationships       //
    // ************************** //

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id');
    }
}
