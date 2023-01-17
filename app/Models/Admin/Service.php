<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $fillable = [
        'name', 'slug', 'description', 'image', 'status', 'service_order', 'url'
    ];

    protected static function boot()
    {
        parent::boot();
        static::deleting(function ($model) {
            $path = 'storage/services/' . $model->image;
            if (\File::exists(public_path() . '/' . $path)) {
                \File::delete($path);
            }
        });
    }

    // ************************** //
    //  Append Extra Attributes   //
    // ************************** //

    protected $appends = ['image_path'];

    public function getImagePathAttribute()
    {
        return $this->attributes['image_path'] = checkImage(asset('storage/services/' . $this->image), 'line-2-img.png');
    }

    public function ServiceLabels()
    {
        return $this->hasMany('App\Models\Admin\ServiceLabel', 'service_id');
    }
}
