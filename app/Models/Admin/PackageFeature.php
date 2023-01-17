<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class PackageFeature extends Model
{
    protected $fillable = [
        'name',
        'info',
        'count',
        'status'
    ];
}
