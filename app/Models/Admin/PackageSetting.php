<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class PackageSetting extends Model
{
    protected $fillable = [
        'name',
        'info',
        'module',
        'start_range',
        'end_range',
        'price_without_vat',
        'price_with_vat',
        'is_voucher_setting',
        'status'
    ];
}
