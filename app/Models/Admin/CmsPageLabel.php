<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CmsPageLabel extends Model
{
    protected $fillable = [
        'cms_page_id', 'label', 'value', 'status'
    ];

    public function cmsPage()
    {
        return $this->belongsTo('App\Models\Admin\CmsPage', 'cms_page_id');
    }
}
