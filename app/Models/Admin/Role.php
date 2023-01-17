<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Role extends Model
{
    protected $fillable = [
        'name', 'right_ids', 'status'
    ];

    public function subAdmins()
    {
        return $this->hasMany('App\Models\Admin\Admin', 'role_id')->whereNotIn('id', [1, auth()->user()->id]);
    }
}
