<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AboutUsContentLabel extends Model
{
    protected $fillable = [
        'about_us_content_id', 'label', 'value', 'status'
    ];

    public function AboutUsContent()
    {
        return $this->belongsTo('App\Models\Admin\AboutUsContent', 'about_us_content_id');
    }
}
