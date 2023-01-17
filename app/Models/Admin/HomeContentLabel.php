<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HomeContentLabel extends Model
{
    protected $fillable = [
        'home_content_id', 'label', 'value', 'status'
    ];

    public function homeContent()
    {
        return $this->belongsTo('App\Models\Admin\HomeContent', 'home_content_id');
    }
}
