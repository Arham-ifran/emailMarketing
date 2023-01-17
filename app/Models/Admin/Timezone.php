<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class Timezone extends Model
{
    protected $fillable = [
        'name', 'utc_offset'
    ];
}
