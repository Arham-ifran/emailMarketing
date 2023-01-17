<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmailTemplateLabel extends Model
{
    protected $fillable = [
        'email_template_id', 'label', 'value', 'status'
    ];

    public function emailTemplate()
    {
        return $this->belongsTo('App\Models\Admin\EmailTemplate', 'email_template_id');
    }
}
