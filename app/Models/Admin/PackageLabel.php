<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PackageLabel extends Model
{
    protected $fillable = [
        'package_id', 'label', 'value', 'status'
    ];

    public function cmsPage()
    {
        return $this->belongsTo('App\Models\Admin\Package', 'package_id');
    }
}
