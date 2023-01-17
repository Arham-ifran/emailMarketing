<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\User;

class Notification extends Model
{
    protected $fillable = [
        'type','user_id', 'message','is_read','link','fa_class'
    ];
    
    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id');
    }
}