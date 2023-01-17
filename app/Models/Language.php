<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Language extends Model
{
    use HasFactory;
    protected $fillable = [
        'name', 'code', 'status'
    ];

    public function totalUsers()
    {
        return User::where('language', '=', $this->code)->count();
    }
}
