<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmailCampaignTemplate extends Model
{
    // use HasFactory;
    protected $fillable = [
        'user_id',
        'name',
        'content',
        'html_content',
        'image',
        'type'
    ];

    protected static function boot()
    {
        parent::boot();
        static::updating(function ($model) {
            $model->updated_at = date('Y-m-d H:i:s');
        });

        static::creating(function ($model) {
            $model->created_at = date('Y-m-d H:i:s');
            $model->user_id = auth()->user()->id;
        });
    }
}
