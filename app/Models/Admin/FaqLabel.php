<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FaqLabel extends Model
{
    protected $fillable = [
        'faq_id', 'label', 'value', 'status'
    ];

    public function cmsPage()
    {
        return $this->belongsTo('App\Models\Admin\Faq', 'faq_id');
    }
}
