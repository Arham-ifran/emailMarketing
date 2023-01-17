<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ServiceLabel extends Model
{
    protected $fillable = [
        'service_id', 'label', 'value', 'status'
    ];

    public function Service()
    {
        return $this->belongsTo('App\Models\Admin\Service', 'service_id');
    }
}
