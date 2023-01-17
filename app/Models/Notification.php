<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'item_id',
        'module',
        'notification_type',
        'notification_text',
        'is_read',
        'redirect_to',
    ];
}
