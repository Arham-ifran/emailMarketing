<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
// use Illuminate\Database\Eloquent\SoftDeletes;

class Contact_group extends Model
{
    use HasFactory;

    protected $fillable = [
        'contact_id', 'group_id', 'user_id'
    ];
}
