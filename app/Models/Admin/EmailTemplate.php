<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class EmailTemplate extends Model
{
    protected $fillable = [
        'type', 'subject', 'content', 'status'
    ];

    public function emailTemplateLabels()
    {
        return $this->hasMany('App\Models\Admin\EmailTemplateLabel', 'email_template_id');
    }
}
