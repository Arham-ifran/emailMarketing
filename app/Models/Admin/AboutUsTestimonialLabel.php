<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AboutUsTestimonialLabel extends Model
{
    protected $fillable = [
        'testimonial_id', 'label', 'value', 'status'
    ];
    protected $table = 'testimonial_labels';

    public function cmsPage()
    {
        return $this->belongsTo('App\Models\Admin\AboutUsTestimonial', 'testimonial_id');
    }
}
