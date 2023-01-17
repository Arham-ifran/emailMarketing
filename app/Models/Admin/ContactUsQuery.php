<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class ContactUsQuery extends Model
{
    protected $fillable = [
        'name', 'email', 'phone', 'subject', 'message', 'status'
    ];
}
