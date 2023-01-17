<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    protected $fillable = [
        'code','name','vat','apply_default_vat','status'
    ];
}
