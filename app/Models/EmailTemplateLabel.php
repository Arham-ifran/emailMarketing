<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmailTemplateLabel extends Model
{
    // use HasFactory;
    protected $fillable = [
        'email_template_id', 'label', 'value', 'status'
    ];

    public function emailTemplate()
    {
        return $this->belongsTo('App\Models\EmailTemplate', 'email_template_id');
    }
}
