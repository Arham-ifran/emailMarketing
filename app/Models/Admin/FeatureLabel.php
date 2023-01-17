<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FeatureLabel extends Model
{
    protected $fillable = [
        'feature_id', 'label', 'value', 'status'
    ];

    public function Feature()
    {
        return $this->belongsTo('App\Models\Admin\Feature', 'feature_id');
    }
}
