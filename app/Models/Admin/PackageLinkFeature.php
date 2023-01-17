<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class PackageLinkFeature extends Model
{
    protected $fillable = [
        'package_id',
        'feature_id',
        'count'
    ];

    public function feature()
    {
        return $this->belongsTo(PackageFeature::class, 'feature_id');
    }
}
