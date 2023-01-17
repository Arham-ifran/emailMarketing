<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CmsPage extends Model
{
    protected $fillable = [
        'title', 'slug', 'content', 'status'
    ];

    public function cmsPageLabels()
    {
        return $this->hasMany('App\Models\Admin\CmsPageLabel', 'cms_page_id');
    }
}
