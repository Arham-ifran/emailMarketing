<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Hashids;

class SmsTemplate extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'name',
        'message',
    ];

    protected $appends = ['hash_id'];
    public function getHashidAttribute()
    {
        return Hashids::encode($this->id);
    }
}
